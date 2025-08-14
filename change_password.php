<?php
session_start();
require 'conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$current = $input['current_password'] ?? '';
$new = $input['new_password'] ?? '';

if (!$current || !$new) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT password FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

$hash = $row['password'];
if (!password_verify($current, $hash)) {
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
    exit();
}

if (strlen($new) < 8) {
    echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters']);
    exit();
}

$newHash = password_hash($new, PASSWORD_BCRYPT);
$upd = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
$upd->bind_param('si', $newHash, $user_id);
if ($upd->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
}
