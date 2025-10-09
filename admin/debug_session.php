<?php
session_start();
require __DIR__ . '/config.php';
require app_path('conn.php');

echo "<h2>Session Debug</h2>";
echo "<p>Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
echo "<p>Session Email: " . ($_SESSION['email'] ?? 'NOT SET') . "</p>";
echo "<p>Session Role: " . ($_SESSION['role'] ?? 'NOT SET') . "</p>";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "<h3>Database Query Results:</h3>";
    
    if (!empty($_SESSION['admin_number'])) {
        $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
            INNER JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? AND ap.admin_number = ? LIMIT 1");
        $stmt->execute([$user_id, $_SESSION['admin_number']]);
    } else {
        $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
            LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
    }
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>Query returned admin data:</p>";
        echo "<ul>";
        echo "<li>Email: " . htmlspecialchars($admin['email'] ?? 'NULL') . "</li>";
        echo "<li>First Name: " . htmlspecialchars($admin['first_name'] ?? 'NULL') . "</li>";
        echo "<li>Last Name: " . htmlspecialchars($admin['last_name'] ?? 'NULL') . "</li>";
        echo "</ul>";
        
        // Test username generation
        $admin['username'] = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
        if (empty($admin['username'])) {
            $admin['username'] = 'Admin User'; // Fallback if no profile data
        }
        
        echo "<p>Generated Username: '" . htmlspecialchars($admin['username']) . "'</p>";
    } else {
        echo "<p>No admin data found for user_id: $user_id</p>";
    }
}
?>