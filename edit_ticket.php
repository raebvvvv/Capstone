<?php
session_start();
require 'conn.php';
header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['request_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing request_id']);
    exit();
}

$query = "UPDATE rental_requests SET student_name = ?, student_id = ?, email = ?, program = ? WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param(
    "sssss",
    $input['student_name'],
    $input['student_id'],
    $input['email'],
    $input['program'],
    $input['request_id']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
?>