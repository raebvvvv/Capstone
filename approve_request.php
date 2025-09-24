<?php
// Secure JSON endpoint to approve a submission (uses PDO)
require __DIR__ . '/config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');
if (function_exists('verify_csrf_header')) { verify_csrf_header(); }
if (function_exists('require_admin')) { require_admin(); }

function respond_error($msg) { echo json_encode(['success'=>false,'error'=>$msg]); exit(); }

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) { respond_error('Invalid payload'); }

// request_id may be alphanumeric (e.g., SRID-2025-...), so keep as string
$requestId = trim((string)($input['request_id'] ?? ''));
$comment = trim((string)($input['comment'] ?? ''));
if (strlen($comment) > 1000) { $comment = substr($comment, 0, 1000); }

if ($requestId === '') { respond_error('Invalid request'); }

try {
    // Determine identifier column: numeric -> submission_id, else submission_code
    $idCol = ctype_digit($requestId) ? 'submission_id' : 'submission_code';
    $sql = "UPDATE submissions
            SET status = 'approved',
                remarks = CASE WHEN :remark <> '' THEN :remark ELSE remarks END,
                reviewer_id = :rid,
                reviewed_at = NOW(),
                status_updated_at = NOW()
            WHERE $idCol = :id";
    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        $info = method_exists($pdo, 'errorInfo') ? implode(' | ', array_filter($pdo->errorInfo() ?: [])) : 'prepare failed';
        throw new Exception($info);
    }
    $ok = $stmt->execute([
        ':remark' => $comment,
        ':rid' => (int)($_SESSION['user_id'] ?? 0),
        ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId
    ]);
    if ($ok) {
        if (function_exists('log_event')) { log_event('APPROVE_REQUEST', 'Request approved', ['request_id' => $requestId]); }
        echo json_encode(['success' => true]);
    } else {
        respond_error('Operation failed');
    }
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Approve failed', ['err' => $e->getMessage(), 'request_id' => $requestId]); }
    respond_error('Operation failed');
}
