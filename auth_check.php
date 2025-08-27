<?php
// Centralized authentication gate.
// Include this at the top of any page that requires an authenticated user.
// Optionally set $requireAdmin = true before including to restrict to admins.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if (!empty($requireAdmin)) {
    if (!isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
        header('Location: login.php');
        exit();
    }
}
