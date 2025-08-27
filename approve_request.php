<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';
header('Content-Type: application/json');
verify_csrf_header();
require_admin();

// Generic helper for uniform error response
function respond_error($msg) { echo json_encode(['success'=>false,'error'=>$msg]); exit(); }

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) { respond_error('Invalid payload'); }
$requestId = isset($input['request_id']) ? (int)$input['request_id'] : 0;
$comment = trim((string)($input['comment'] ?? ''));
if (strlen($comment) > 1000) { $comment = substr($comment,0,1000); }

if ($requestId <= 0) { respond_error('Invalid request'); }

// Update status to Approved; store reviewer comment in remark
if ($stmt = $conn->prepare("UPDATE rental_requests SET status='Approved', remark = ?, approved_timestamp = NOW() WHERE request_id = ?")) {
    $stmt->bind_param('si', $comment, $requestId);
    if ($stmt->execute()) {
        log_event('APPROVE_REQUEST', 'Request approved', ['request_id' => $requestId]);
        echo json_encode(['success' => true]);
    } else {
        log_event('DB_ERROR','Approve execute failed',['err'=>$stmt->error,'request_id'=>$requestId]);
        respond_error('Operation failed');
    }
    $stmt->close();
} else {
    log_event('DB_ERROR','Approve prepare failed',['err'=>$conn->error]);
    respond_error('Operation failed');
}
