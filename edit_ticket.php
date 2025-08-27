<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';
header('Content-Type: application/json');
verify_csrf_header();
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$requestId = isset($input['request_id']) ? (int)$input['request_id'] : 0;
$studentName = trim((string)($input['student_name'] ?? ''));
$studentId = trim((string)($input['student_id'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$program = trim((string)($input['program'] ?? ''));

if ($requestId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid request_id']);
    exit();
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit();
}

if ($stmt = $conn->prepare("UPDATE rental_requests SET student_name = ?, student_id = ?, email = ?, program = ? WHERE request_id = ?")) {
    $stmt->bind_param('ssssi', $studentName, $studentId, $email, $program, $requestId);
    if ($stmt->execute()) {
        log_event('EDIT_REQUEST', 'Request edited', ['request_id' => $requestId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB error']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Prepare failed']);
}
?>