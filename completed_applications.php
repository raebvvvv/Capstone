<?php
// Moved: redirect to /admin/completed_applications.php
header('Location: admin/completed_applications.php');
exit;
// Check if the user is logged in and is an admin

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {

    header("Location: login.php");

    exit();

}

