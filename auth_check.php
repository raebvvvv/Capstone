<?php
// Centralized authentication gate.
// Include this at the top of any page that requires an authenticated user.
// Optionally set $requireAdmin = true before including to restrict to admins.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // When included from a page inside User/Afterlogin/, redirect to sibling 404 page.
    // Use relative '404.php' so we don't duplicate directory segments.
    header('Location: 404.php');
    exit();
}

if (!empty($requireAdmin)) {
    if (!isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
        header('Location: 404.php');
        exit();
    }
}

// Enforce no-store on authenticated pages to mitigate browser back caching after logout
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
}
