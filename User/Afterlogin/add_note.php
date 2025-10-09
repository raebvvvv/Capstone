<?php
// User note submission endpoint with constraints and safeguards
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

if (function_exists('secure_bootstrap')) { secure_bootstrap(); }

header('Content-Type: application/json');

// mbstring polyfills (hosting may not have mbstring enabled)
if (!function_exists('mb_strlen')) {
	function mb_strlen($s) { return strlen($s); }
}
if (!function_exists('mb_substr')) {
	function mb_substr($s, $start, $len = null) { return ($len === null) ? substr($s, $start) : substr($s, $start, $len); }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'error' => 'Method not allowed']);
	exit;
}

// CSRF validation: prefer JSON error instead of plain text exit
if (function_exists('csrf_token')) {
	$posted = $_POST['csrf_token'] ?? '';
	$headerTok = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_CSRFTOKEN'] ?? '');
	$sessionTok = $_SESSION['csrf_token'] ?? '';
	$ok = ($posted && hash_equals($sessionTok, $posted)) || ($headerTok && hash_equals($sessionTok, $headerTok));
	if (!$ok) {
		http_response_code(400);
		echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
		exit;
	}
} else if (function_exists('verify_csrf_post')) {
	// Fallback to legacy function if helpers are not available
	try {
		verify_csrf_post();
	} catch (Throwable $e) {
		http_response_code(400);
		echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
		exit;
	}
}

$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
	http_response_code(401);
	echo json_encode(['success' => false, 'error' => 'Not authenticated']);
	exit;
}

// Validate inputs
$submissionCode = trim((string)($_POST['submission_code'] ?? ''));
$note = trim((string)($_POST['note'] ?? ''));
if ($submissionCode === '' || $note === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'error' => 'Submission code and note are required']);
	exit;
}

// Enforce length limit and simple content rules
if (mb_strlen($note) > 1000) {
	$note = mb_substr($note, 0, 1000);
}

// Resolve submission and ownership
$stmt = $pdo->prepare('SELECT submission_id, status FROM submissions WHERE submission_code = ? AND user_id = ? LIMIT 1');
$stmt->execute([$submissionCode, $userId]);
$sub = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sub) {
	http_response_code(403);
	echo json_encode(['success' => false, 'error' => 'Submission not found or access denied']);
	exit;
}

$submissionId = (int)$sub['submission_id'];
$status = strtolower((string)$sub['status']);

// Status gating: allow while not completed/withdrawn
$allowedStatuses = ['pending','pending_review','under_review','revision_needed','approved'];
if (!in_array($status, $allowedStatuses, true)) {
	http_response_code(400);
	echo json_encode(['success' => false, 'error' => 'Notes are not allowed for this submission status']);
	exit;
}

// Ensure tables exist (idempotent)
try {
	$pdo->exec("CREATE TABLE IF NOT EXISTS submission_notes (
		id INT AUTO_INCREMENT PRIMARY KEY,
		submission_id INT NOT NULL,
		user_id INT NOT NULL,
		note TEXT NOT NULL,
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		CONSTRAINT fk_sn_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
} catch (Throwable $e) { /* ignore */ }

// Rate limits
$COOLDOWN_SEC = 600; // 10 minutes per submission
$PER_SUBMISSION_DAILY = 3; // per submission per day
$GLOBAL_DAILY = 10; // per user per day across all submissions

// Last note cooldown (per submission)
$stmt = $pdo->prepare('SELECT created_at FROM submission_notes WHERE submission_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$submissionId, $userId]);
$last = $stmt->fetchColumn();
if ($last) {
	$lastTs = strtotime($last);
	$diff = time() - (int)$lastTs;
	if ($diff < $COOLDOWN_SEC) {
		http_response_code(429);
		echo json_encode(['success' => false, 'error' => 'Please wait before sending another note.', 'cooldown_seconds' => $COOLDOWN_SEC - $diff]);
		exit;
	}
}

// Daily caps
$today = date('Y-m-d');
$stmt = $pdo->prepare('SELECT COUNT(*) FROM submission_notes WHERE user_id = ? AND submission_id = ? AND DATE(created_at) = ?');
$stmt->execute([$userId, $submissionId, $today]);
$countSubToday = (int)$stmt->fetchColumn();
if ($countSubToday >= $PER_SUBMISSION_DAILY) {
	http_response_code(429);
	echo json_encode(['success' => false, 'error' => 'Daily note limit reached for this submission.']);
	exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM submission_notes WHERE user_id = ? AND DATE(created_at) = ?');
$stmt->execute([$userId, $today]);
$countGlobalToday = (int)$stmt->fetchColumn();
if ($countGlobalToday >= $GLOBAL_DAILY) {
	http_response_code(429);
	echo json_encode(['success' => false, 'error' => 'Daily note limit reached.']);
	exit;
}

// Insert note
$ins = $pdo->prepare('INSERT INTO submission_notes (submission_id, user_id, note, created_at) VALUES (?, ?, ?, NOW())');
$ins->execute([$submissionId, $userId, $note]);

// Response with simple echo of created note
$createdAt = date('Y-m-d H:i:s');
$remainingSub = max(0, $PER_SUBMISSION_DAILY - ($countSubToday + 1));
$remainingGlobal = max(0, $GLOBAL_DAILY - ($countGlobalToday + 1));

echo json_encode([
	'success' => true,
	'note' => [
		'note' => $note,
		'created_at' => $createdAt
	],
	'limits' => [
		'cooldown_sec' => $COOLDOWN_SEC,
		'per_submission_daily_remaining' => $remainingSub,
		'global_daily_remaining' => $remainingGlobal
	]
]);
exit;
?>
