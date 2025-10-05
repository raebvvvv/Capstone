<?php
// Profile Helper Functions - Defensive programming for profile operations
// This file provides safe profile data retrieval with error handling

function get_user_profile_safe($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id, 
                u.email, 
                u.role, 
                u.status,
                CASE 
                    WHEN u.role = 'student' THEN sp.student_number
                    WHEN u.role = 'employee' THEN ep.employee_number  
                    WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
                    ELSE CONCAT('User-', u.user_id)
                END as identifier,
                CASE 
                    WHEN u.role = 'student' THEN COALESCE(CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name), sp.first_name, u.email)
                    WHEN u.role = 'employee' THEN COALESCE(CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name), ep.first_name, u.email)
                    WHEN u.role = 'admin' THEN COALESCE(CONCAT_WS(' ', ap.first_name, ap.last_name), ap.first_name, u.email)
                    ELSE u.email
                END as full_name,
                CASE 
                    WHEN u.role = 'student' THEN sp.first_name
                    WHEN u.role = 'employee' THEN ep.first_name
                    WHEN u.role = 'admin' THEN ap.first_name
                    ELSE NULL
                END as first_name
            FROM users u
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
            LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
            WHERE u.user_id = ?
        ");
        
        $stmt->execute([$user_id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$profile) {
            return ['error' => 'User not found', 'user_id' => $user_id];
        }
        
        // Ensure we have a name fallback
        if (empty($profile['full_name']) || $profile['full_name'] === $profile['email']) {
            $profile['full_name'] = 'User #' . $user_id;
        }
        
        return $profile;
        
    } catch (PDOException $e) {
        error_log("Profile retrieval error for user $user_id: " . $e->getMessage());
        return ['error' => 'Database error', 'user_id' => $user_id];
    }
}

function ensure_profile_exists($pdo, $user_id, $role) {
    try {
        // Check if profile exists for this user
        switch ($role) {
            case 'student':
                $check = $pdo->prepare("SELECT user_id FROM student_profiles WHERE user_id = ?");
                break;
            case 'employee':
                $check = $pdo->prepare("SELECT user_id FROM employee_profiles WHERE user_id = ?");
                break;
            case 'admin':
                $check = $pdo->prepare("SELECT user_id FROM admin_profiles WHERE user_id = ?");
                break;
            default:
                return false;
        }
        
        $check->execute([$user_id]);
        if ($check->fetch()) {
            return true; // Profile already exists
        }
        
        // Create missing profile with minimal data
        switch ($role) {
            case 'student':
                $create = $pdo->prepare("INSERT INTO student_profiles (user_id, first_name, created_at) VALUES (?, 'User', NOW())");
                break;
            case 'employee':
                $create = $pdo->prepare("INSERT INTO employee_profiles (user_id, first_name, created_at) VALUES (?, 'User', NOW())");
                break;
            case 'admin':
                $create = $pdo->prepare("INSERT INTO admin_profiles (user_id, first_name, created_at) VALUES (?, 'Admin', NOW())");
                break;
        }
        
        return $create->execute([$user_id]);
        
    } catch (PDOException $e) {
        error_log("Profile creation error for user $user_id ($role): " . $e->getMessage());
        return false;
    }
}

function safe_profile_field($profile, $field, $default = '') {
    return isset($profile[$field]) && !empty($profile[$field]) ? htmlspecialchars($profile[$field]) : htmlspecialchars($default);
}
?>