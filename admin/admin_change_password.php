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
$profileId = isset($input['profile_id']) && ctype_digit((string)$input['profile_id']) ? (int)$input['profile_id'] : 0;
if(!$current || !$new){ echo json_encode(['success'=>false,'error'=>'Missing fields']); exit(); }

// Resolve target user via admin_profiles.profile_id (if provided) or infer from current admin's profile
$sessionUserId = (int)$_SESSION['user_id'];
$targetUserId = null;
if ($profileId > 0) {
    $mp = $pdo->prepare('SELECT user_id FROM admin_profiles WHERE profile_id = ? LIMIT 1');
    $mp->execute([$profileId]);
    $map = $mp->fetch(PDO::FETCH_ASSOC);
    if (!$map) { echo json_encode(['success'=>false,'error'=>'Profile not found']); exit(); }
    if ((int)$map['user_id'] !== $sessionUserId) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit(); }
    $targetUserId = (int)$map['user_id'];
} else {
    // Infer latest admin profile for this user
    $mp = $pdo->prepare('SELECT profile_id, user_id FROM admin_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1');
    $mp->execute([$sessionUserId]);
    $map = $mp->fetch(PDO::FETCH_ASSOC);
    if (!$map) { echo json_encode(['success'=>false,'error'=>'Admin profile not found']); exit(); }
    $profileId = (int)$map['profile_id'];
    $targetUserId = (int)$map['user_id'];
}

// Use PDO ($pdo) from conn.php
$stmt = $pdo->prepare('SELECT password FROM users WHERE user_id = ? LIMIT 1');
$stmt->execute([$targetUserId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ echo json_encode(['success'=>false,'error'=>'User not found']); exit(); }
if(!password_verify($current, $row['password'])) { log_event('ADMIN_PASSWORD_CHANGE_FAIL','Incorrect current password'); echo json_encode(['success'=>false,'error'=>'Current password is incorrect']); exit(); }
if(strlen($new) < 12){ log_event('ADMIN_PASSWORD_CHANGE_FAIL','Too short'); echo json_encode(['success'=>false,'error'=>'New password must be at least 12 characters']); exit(); }
if(strlen($new) > 200){ echo json_encode(['success'=>false,'error'=>'Password too long']); exit(); }

$newHash = password_hash($new, PASSWORD_BCRYPT);
$upd = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
try {
    $ok = $upd->execute([$newHash, $targetUserId]);
    if($ok) { if(function_exists('log_event')) { log_event('ADMIN_PASSWORD_CHANGE','Password changed'); } echo json_encode(['success'=>true]); }
    else { if(function_exists('log_event')) { log_event('ADMIN_PASSWORD_CHANGE_FAIL','DB update failed'); } echo json_encode(['success'=>false,'error'=>'Operation failed']); }
} catch (Throwable $e) {
    if(function_exists('log_event')) { log_event('ADMIN_PASSWORD_CHANGE_FAIL','DB exception',['err'=>$e->getMessage()]); }
    echo json_encode(['success'=>false,'error'=>'Operation failed']);
}
