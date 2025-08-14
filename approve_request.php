<?php
session_start();
require 'conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$requestId = $input['request_id'] ?? null;
$comment = $input['comment'] ?? '';

if (!$requestId) {
    echo json_encode(['success' => false, 'error' => 'Missing request_id']);
    exit();
}

// Approve and store optional comment in remark field (adjust column if different)
$stmt = $conn->prepare("UPDATE rental_requests SET status='Approved', remark = ?, approved_timestamp = NOW() WHERE request_id = ?");
$stmt->bind_param('si', $comment, $requestId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
