<?php
// User profile update endpoint (non-admin). Admins use admin/admin_update_profile.php.
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';
header('Content-Type: application/json');
verify_csrf_header();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit();
}

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

// Get user role to determine which profile table to update
$role_query = $conn->prepare('SELECT role FROM users WHERE user_id = ? LIMIT 1');
$role_query->bind_param('i', $user_id);
$role_query->execute();
$role_result = $role_query->get_result();
$user_data = $role_result->fetch_assoc();

if (!$user_data) {
    echo json_encode(['success'=>false,'error'=>'User not found']);
    exit();
}

$user_role = $user_data['role'];

// Check for email uniqueness (optional, ignoring same user)
$chk = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id <> ? LIMIT 1');
$chk->bind_param('si', $email, $user_id);
$chk->execute();
$r = $chk->get_result();
if($r && $r->fetch_assoc()) { 
    echo json_encode(['success'=>false,'error'=>'Email already in use']); 
    exit(); 
}

// Begin transaction for updating both users table and profile table
$conn->begin_transaction();

try {
    // Update email in users table
    $upd_user = $conn->prepare('UPDATE users SET email = ? WHERE user_id = ?');
    $upd_user->bind_param('si', $email, $user_id);
    if (!$upd_user->execute()) {
        throw new Exception('Failed to update user email');
    }
    
    // Update name in appropriate profile table based on role
    if ($user_role === 'student') {
        // Assume name is stored as first_name for simplicity, or split if needed
        $upd_profile = $conn->prepare('UPDATE student_profiles SET first_name = ?, email = ? WHERE user_id = ?');
        $upd_profile->bind_param('ssi', $name, $email, $user_id);
    } elseif ($user_role === 'employee') {
        $upd_profile = $conn->prepare('UPDATE employee_profiles SET first_name = ?, email = ? WHERE user_id = ?');
        $upd_profile->bind_param('ssi', $name, $email, $user_id);
    } elseif ($user_role === 'admin') {
        $upd_profile = $conn->prepare('UPDATE admin_profiles SET first_name = ? WHERE user_id = ?');
        $upd_profile->bind_param('si', $name, $user_id);
    } else {
        throw new Exception('Unknown user role');
    }
    
    if (isset($upd_profile) && !$upd_profile->execute()) {
        throw new Exception('Failed to update profile');
    }
    
    // Commit transaction
    $conn->commit();
    
    if(function_exists('log_event')) { 
        log_event('USER_PROFILE_UPDATE', 'Profile updated', ['uid'=>$user_id, 'role'=>$user_role]); 
    }
    
    // Refresh values in session if stored
    $_SESSION['email'] = $email;
    if (isset($_SESSION['username'])) {
        $_SESSION['username'] = $name;
    }
    
    echo json_encode(['success'=>true,'name'=>$name,'email'=>$email]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    if(function_exists('log_event')) { 
        log_event('USER_PROFILE_UPDATE_FAIL', 'Profile update failed', ['uid'=>$user_id, 'error'=>$e->getMessage()]); 
    }
    echo json_encode(['success'=>false,'error'=>'Update failed']);
}
