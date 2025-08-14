<?php
require 'conn.php'; // Include your database connection file

// Function to get active users count
function getActiveUsersCount($conn) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE status = 'approved'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

// Return JSON response
echo json_encode(['count' => getActiveUsersCount($conn)]);
?>
