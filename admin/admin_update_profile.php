<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require_admin();
require __DIR__ . '/../conn.php';
header('Content-Type: application/json');
verify_csrf_header();

if (!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit(); }

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if(!is_array($input)) { echo json_encode(['success'=>false,'error'=>'Invalid payload']); exit(); }
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
if(!$name || !$email) { echo json_encode(['success'=>false,'error'=>'Name and email required']); exit(); }
if(strlen($name) > 150) { echo json_encode(['success'=>false,'error'=>'Name too long']); exit(); }
if(strlen($email) > 200) { echo json_encode(['success'=>false,'error'=>'Email too long']); exit(); }
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { echo json_encode(['success'=>false,'error'=>'Invalid email']); exit(); }

$user_id = (int)$_SESSION['user_id'];
// Ensure email uniqueness beyond current user
$chk = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id <> ? LIMIT 1');
$chk->bind_param('si',$email,$user_id);
$chk->execute();
$r = $chk->get_result();
if($r && $r->fetch_assoc()) { echo json_encode(['success'=>false,'error'=>'Email already in use']); exit(); }

$upd = $conn->prepare('UPDATE users SET username = ?, email = ? WHERE user_id = ?');
$upd->bind_param('ssi',$name,$email,$user_id);
if($upd->execute()) {
    if(function_exists('log_event')) { log_event('ADMIN_PROFILE_UPDATE','Profile updated',['uid'=>$user_id]); }
    $_SESSION['username'] = $name; $_SESSION['email'] = $email;
    echo json_encode(['success'=>true,'name'=>$name,'email'=>$email]);
} else {
    if(function_exists('log_event')) { log_event('ADMIN_PROFILE_UPDATE_FAIL','DB update failed',['err'=>$upd->error]); }
    echo json_encode(['success'=>false,'error'=>'Update failed']);
}
