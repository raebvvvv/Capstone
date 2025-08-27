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
    $query = 'DELETE FROM users WHERE user_id = ? LIMIT 1';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'User deleted successfully.';
        log_event('USER_DELETE', 'Deleted user', ['target_user_id' => $user_id]);
    } else {
        $_SESSION['error'] = 'Error deleting user.';
    }
    $stmt->close();
}
header('Location: manageuser.php');
exit();
?>
