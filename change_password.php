<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
// Simple rate limiting (issue #22): allow max 10 password change attempts per user per hour
$now = time();
if (!isset($_SESSION['pw_rl'])) { $_SESSION['pw_rl'] = []; }
$_SESSION['pw_rl'] = array_filter($_SESSION['pw_rl'], function($ts) use ($now){ return ($now - $ts) < 3600; });
if (count($_SESSION['pw_rl']) >= 10) {
    echo json_encode(['success'=>false,'error'=>'Too many attempts. Try later.']);
    exit();
}
$_SESSION['pw_rl'][] = $now;
require 'conn.php';
header('Content-Type: application/json');
verify_csrf_header();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) { echo json_encode(['success'=>false,'error'=>'Invalid payload']); exit(); }
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
    log_event('PASSWORD_CHANGE_FAIL', 'Incorrect current password');
    exit();
}

if (strlen($new) < 8) { echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters']); log_event('PASSWORD_CHANGE_FAIL', 'New password too short'); exit(); }
if (strlen($new) > 200) { echo json_encode(['success'=>false,'error'=>'Password too long']); exit(); }

$newHash = password_hash($new, PASSWORD_BCRYPT);
$upd = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
$upd->bind_param('si', $newHash, $user_id);
if ($upd->execute()) {
    log_event('PASSWORD_CHANGE', 'Password changed');
    echo json_encode(['success' => true]);
} else {
    log_event('PASSWORD_CHANGE_FAIL', 'DB update failed', ['err'=>$upd->error]);
    echo json_encode(['success' => false, 'error' => 'Operation failed']);
}
