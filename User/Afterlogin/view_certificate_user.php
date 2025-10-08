<?php
// User-facing certificate viewer: serves PDF if the logged-in user owns the submission.
require __DIR__ . '/../../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
// Basic auth: must be logged in (any role). We reuse session variable user_logged_in and user_id.
if (empty($_SESSION['user_logged_in']) || empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo 'Not authorized';
    exit;
}
$userId = (int)$_SESSION['user_id'];
$req = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
if ($req === '') { http_response_code(400); echo 'Missing id'; exit; }
$mode = isset($_GET['mode']) ? strtolower(trim((string)$_GET['mode'])) : 'inline';
$isNumeric = ctype_digit($req);
$col = $isNumeric ? 'submission_id' : 'submission_code';
$stmt = $pdo->prepare("SELECT submission_id, user_id FROM submissions WHERE $col = ? LIMIT 1");
$stmt->execute([$isNumeric ? (int)$req : $req]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); echo 'Submission not found'; exit; }
if ((int)$row['user_id'] !== $userId) {
    // Hide existence: act like 404
    http_response_code(404); echo 'Submission not found'; exit;
}
$submissionId = (int)$row['submission_id'];
require_once app_path('includes/certificate_generator.php');
// Allow same tuning params only for admins; for users we ignore coordinate overrides for safety.
$options = [];
if (!empty($_GET['force']) && !empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) { $options['force_regen'] = true; }
if (isset($_GET['authors_align'])) { $options['authors_align'] = $_GET['authors_align']; }
try {
    $pdfPath = generate_certificate($pdo, $submissionId, $options);
} catch (Throwable $e) {
    http_response_code(500);
    if (function_exists('log_event')) { log_event('CERT_ERROR_USER','Generate failed',['sid'=>$submissionId,'err'=>$e->getMessage()]); }
    echo 'Certificate generation failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
if (!is_file($pdfPath)) { http_response_code(500); echo 'Certificate file missing'; exit; }
header('Cache-Control: private, max-age=3600');
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($pdfPath));
$fname = 'certificate-' . basename($pdfPath);
if ($mode === 'download') {
    header('Content-Disposition: attachment; filename="' . $fname . '"');
} else {
    header('Content-Disposition: inline; filename="' . $fname . '"');
}
readfile($pdfPath);
exit;