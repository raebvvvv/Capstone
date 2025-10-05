<?php
require __DIR__ . '/config.php';
require app_path('conn.php');

// Check current admin profile state
$stmt = $pdo->prepare("SELECT u.user_id, u.email, u.role, ap.first_name, ap.last_name FROM users u 
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.role = 'admin'");
$stmt->execute();
$admins = $stmt->fetchAll();

echo "<h2>Admin Profile Debug</h2>";
echo "<table border='1'>";
echo "<tr><th>User ID</th><th>Email</th><th>Role</th><th>First Name</th><th>Last Name</th><th>Generated Username</th></tr>";

foreach ($admins as $admin) {
    $username = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
    if (empty($username)) {
        $username = 'Admin User'; // Fallback if no profile data
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($admin['user_id']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['first_name'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($admin['last_name'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($username) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>