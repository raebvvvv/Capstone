<?php
session_start();
require 'conn.php'; // Include your database connection file

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Reset AUTO_INCREMENT value
        $query_reset_ai = "SELECT MAX(user_id) AS max_id FROM users";
        $result = $conn->query($query_reset_ai);
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

        // Construct the ALTER TABLE query with the new AUTO_INCREMENT value
        $query_set_ai = "ALTER TABLE users AUTO_INCREMENT = $max_id";
        $conn->query($query_set_ai);

        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting user.";
    }
}

header("Location: manageuser.php");
exit();
?>