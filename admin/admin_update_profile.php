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
$profileId = isset($input['profile_id']) && ctype_digit((string)$input['profile_id']) ? (int)$input['profile_id'] : 0;
if(!$name) { echo json_encode(['success'=>false,'error'=>'Name is required']); exit(); }
if(strlen($name) > 150) { echo json_encode(['success'=>false,'error'=>'Name too long']); exit(); }

// Resolve and authorize via admin_profiles.profile_id
$sessionUserId = (int)$_SESSION['user_id'];
 $sessionAdminNumber = isset($_SESSION['admin_number']) ? trim((string)$_SESSION['admin_number']) : null;
if ($profileId > 0) {
    $mp = $pdo->prepare('SELECT user_id, admin_number FROM admin_profiles WHERE profile_id = ? LIMIT 1');
    $mp->execute([$profileId]);
    $map = $mp->fetch(PDO::FETCH_ASSOC);
    if (!$map) { echo json_encode(['success'=>false,'error'=>'Profile not found']); exit(); }
    if ((int)$map['user_id'] !== $sessionUserId) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit(); }
    if ($sessionAdminNumber !== null && isset($map['admin_number']) && (string)$map['admin_number'] !== (string)$sessionAdminNumber) {
        echo json_encode(['success'=>false,'error'=>'Profile mismatch']); exit();
    }
} else {
    if ($sessionAdminNumber !== null) {
        $mp = $pdo->prepare('SELECT profile_id FROM admin_profiles WHERE user_id = ? AND admin_number = ? LIMIT 1');
        $mp->execute([$sessionUserId, $sessionAdminNumber]);
    } else {
        $mp = $pdo->prepare('SELECT profile_id FROM admin_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1');
        $mp->execute([$sessionUserId]);
    }
    $map = $mp->fetch(PDO::FETCH_ASSOC);
    if (!$map) { echo json_encode(['success'=>false,'error'=>'Admin profile not found']); exit(); }
    $profileId = (int)$map['profile_id'];
}

// Enforce: admin email cannot be changed. Compare with current email and reject attempts.
$curr = $pdo->prepare('SELECT email FROM users WHERE user_id = ? LIMIT 1');
$curr->execute([$sessionUserId]);
$currRow = $curr->fetch(PDO::FETCH_ASSOC);
$currentEmail = $currRow['email'] ?? ($_SESSION['email'] ?? '');
if ($email !== '' && strcasecmp($email, (string)$currentEmail) !== 0) {
    echo json_encode(['success'=>false,'error'=>'Admin email cannot be changed']);
    exit();
}

// Parse name into first and last name (simple split on last space)
$nameParts = explode(' ', trim($name));
$firstName = '';
$lastName = '';
if (count($nameParts) >= 2) {
    $lastName = array_pop($nameParts);
    $firstName = implode(' ', $nameParts);
} else {
    $firstName = $name;
    $lastName = '';
}

// Update name in admin_profiles table strictly by profile_id and current owner
 $updProfile = $pdo->prepare('UPDATE admin_profiles SET first_name = ?, last_name = ? WHERE profile_id = ? AND user_id = ?' . ($sessionAdminNumber !== null ? ' AND admin_number = ?' : ''));

try {
    // Update profile if it exists (scoped to owner and admin_number)
    $params = [$firstName, $lastName, $profileId, $sessionUserId];
    if ($sessionAdminNumber !== null) { $params[] = $sessionAdminNumber; }
    $ok = $updProfile->execute($params);
    if ($ok) {
        if(function_exists('log_event')) { log_event('ADMIN_PROFILE_UPDATE','Profile updated',['uid'=>$sessionUserId,'profile_id'=>$profileId]); }
        // Keep session email unchanged (locked)
        $_SESSION['email'] = $currentEmail;
        echo json_encode(['success'=>true,'name'=>$name,'email'=>$currentEmail]);
    } else {
        if(function_exists('log_event')) { log_event('ADMIN_PROFILE_UPDATE_FAIL','DB update failed'); }
        echo json_encode(['success'=>false,'error'=>'Update failed']);
    }
} catch (Throwable $e) {
    if(function_exists('log_event')) { log_event('ADMIN_PROFILE_UPDATE_FAIL','DB exception',['err'=>$e->getMessage()]); }
    echo json_encode(['success'=>false,'error'=>'Update failed']);
}
