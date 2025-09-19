<?php
$servername = "localhost";
$username = "root";   // Use your database username
$password = "";       // Use your database password
$dbname = "hasmin_users"; // Replace with your database name
$port=3306;


// Create connection
$conn = @new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    // Generic message (avoid leaking internal details). Log internally if logger loaded later.
    error_log('DB connection failed');
    // Provide minimal stub object so includes can continue (pages should handle missing data gracefully)
    die('Service temporarily unavailable. Please try again later.');
}
?>
