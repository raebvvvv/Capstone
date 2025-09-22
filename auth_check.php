<?php
// Centralized authentication gate.
// Include this at the top of any page that requires an authenticated user.
// Optionally set $requireAdmin = true before including to restrict to admins.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check for valid session with user_id
if (!isset($_SESSION['user_id'])) {
    // Use absolute paths from document root
    header('Location: /Capstone/User/Beforelogin/login.php');
    exit();
}

// Validate user exists in database (prevents session fixation)
require_once __DIR__ . '/config.php';
$stmt = $pdo->prepare("SELECT status FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['status'] !== 'active') {
    session_destroy();
    header('Location: /Capstone/User/Beforelogin/login.php');
    exit();
}

// Admin check if required
if (!empty($requireAdmin) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header('Location: /Capstone/User/Beforelogin/404.php');
    exit();
}

// Set security headers
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Additional security headers
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
