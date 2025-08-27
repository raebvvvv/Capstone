<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php'; // DB connection

// Use unified helper for admin enforcement
require_admin();

// Fetch completed requests (explicit columns) from current requests table
$query = "SELECT request_id, student_id, student_name, user_classification, program, request_date, status, remark, completed_timestamp FROM rental_requests WHERE status = 'Completed'";
$result = $conn->query($query);
if (!$result) {
    log_event('DB_ERROR', 'Completed tickets query failed', ['error' => $conn->error]);
}
?>
