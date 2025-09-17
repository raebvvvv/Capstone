<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require_admin();
require __DIR__ . '/../conn.php';
header('Content-Type: application/json');
verify_csrf_header();

// Rate limit (reuse logic)
$now = time();
if (!isset($_SESSION['pw_rl_admin'])) { $_SESSION['pw_rl_admin'] = []; }
$_SESSION['pw_rl_admin'] = array_filter($_SESSION['pw_rl_admin'], fn($ts) => ($now - $ts) < 3600);
if (count($_SESSION['pw_rl_admin']) >= 10) {
    echo json_encode(['success'=>false,'error'=>'Too many attempts. Try later.']);
    exit();
}
$_SESSION['pw_rl_admin'][] = $now;

if (!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit(); }

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if(!is_array($input)) { echo json_encode(['success'=>false,'error'=>'Invalid payload']); exit(); }
$current = $input['current_password'] ?? '';
$new = $input['new_password'] ?? '';
if(!$current || !$new){ echo json_encode(['success'=>false,'error'=>'Missing fields']); exit(); }

$user_id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT password FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i',$user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if(!$row){ echo json_encode(['success'=>false,'error'=>'User not found']); exit(); }
if(!password_verify($current, $row['password'])) { log_event('ADMIN_PASSWORD_CHANGE_FAIL','Incorrect current password'); echo json_encode(['success'=>false,'error'=>'Current password is incorrect']); exit(); }
if(strlen($new) < 8){ log_event('ADMIN_PASSWORD_CHANGE_FAIL','Too short'); echo json_encode(['success'=>false,'error'=>'New password must be at least 8 characters']); exit(); }
if(strlen($new) > 200){ echo json_encode(['success'=>false,'error'=>'Password too long']); exit(); }

$newHash = password_hash($new, PASSWORD_BCRYPT);
$upd = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
$upd->bind_param('si',$newHash,$user_id);
if($upd->execute()) { log_event('ADMIN_PASSWORD_CHANGE','Password changed'); echo json_encode(['success'=>true]); }
else { log_event('ADMIN_PASSWORD_CHANGE_FAIL','DB update failed',['err'=>$upd->error]); echo json_encode(['success'=>false,'error'=>'Operation failed']); }
