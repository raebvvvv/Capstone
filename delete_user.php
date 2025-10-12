<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';

// Ensure admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
    header('Location: login.php');
    exit();
}

// Enforce POST with CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manageuser.php');
    exit();
}
verify_csrf_post();

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($user_id > 0) {
    // Use transaction to delete child rows first, then user
    $conn->begin_transaction();
    try {
        // Delete from profile tables explicitly (if present)
        if ($stmt = $conn->prepare('DELETE FROM student_profiles WHERE user_id = ?')) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();
        }
        if ($stmt = $conn->prepare('DELETE FROM employee_profiles WHERE user_id = ?')) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();
        }
        if ($stmt = $conn->prepare('DELETE FROM admin_profiles WHERE user_id = ?')) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();
        }
        // Finally delete from users
        if ($stmt = $conn->prepare('DELETE FROM users WHERE user_id = ? LIMIT 1')) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();
        }
        $conn->commit();
        $_SESSION['success'] = 'User deleted successfully.';
        if (function_exists('log_event')) { log_event('USER_DELETE', 'Deleted user', ['target_user_id' => $user_id]); }
    } catch (Throwable $e) {
        $conn->rollback();
        $_SESSION['error'] = 'Error deleting user.';
    }
}
header('Location: manageuser.php');
exit();
?>
