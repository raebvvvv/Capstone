<?php
// Centralized authentication gate.
// Include this at the top of any page that requires an authenticated user.
// Optionally set $requireAdmin = true before including to restrict to admins.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// --- Authentication check ---
if (!isset($_SESSION['user_id'])) {
    // Use absolute paths from document root
    header('Location: /Capstone/User/Beforelogin/login.php');
    exit();
}

// Validate user exists in database (prevents session fixation)
require_once __DIR__ . '/config.php';
$stmt = $pdo->prepare("SELECT status, role FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['status'] !== 'active') {
    session_destroy();
    header('Location: /Capstone/User/Beforelogin/login.php');
    exit();
}

// --- Admin restriction ---
if (!empty($requireAdmin)) {
    // Prefer database role, but fall back to session if needed
    $isAdmin = (isset($user['role']) && $user['role'] === 'admin') 
               || (isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1);

    if (!$isAdmin) {
        header('Location: /Capstone/User/Beforelogin/404.php');
        exit();
    }
}

// --- Security headers ---
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Additional security headers
    // Allow same-origin framing so admin pages can embed internal certificate PDFs.
    // If you want to force DENY on a specific page, define FRAME_OPT_DENY before including this file.
    // Safely resolve optional override constant without triggering notices
    $__frameDeny = false;
    if (defined('FRAME_OPT_DENY')) {
        try { $__frameDeny = (constant('FRAME_OPT_DENY') === true); } catch (Throwable $____e) { $__frameDeny = false; }
    }
    header('X-Frame-Options: ' . ($__frameDeny ? 'DENY' : 'SAMEORIGIN'));
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
}

// --- CSRF token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
