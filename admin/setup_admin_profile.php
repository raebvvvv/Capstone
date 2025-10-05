<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');

// This script helps create admin profiles for existing admin users

echo "<h2>Admin Profile Setup</h2>";

// Get all admin users without profiles
$stmt = $pdo->query("
    SELECT u.user_id, u.email 
    FROM users u 
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id 
    WHERE u.role = 'admin' AND ap.user_id IS NULL
");
$admins_without_profiles = $stmt->fetchAll();

if (empty($admins_without_profiles)) {
    echo "<p>✅ All admin users already have profiles!</p>";
    
    // Show existing admin profiles
    $stmt = $pdo->query("
        SELECT u.email, ap.first_name, ap.last_name 
        FROM users u 
        JOIN admin_profiles ap ON u.user_id = ap.user_id 
        WHERE u.role = 'admin'
    ");
    $existing_profiles = $stmt->fetchAll();
    
    if ($existing_profiles) {
        echo "<h3>Existing Admin Profiles:</h3><ul>";
        foreach ($existing_profiles as $profile) {
            echo "<li>" . htmlspecialchars($profile['email']) . " - " . 
                 htmlspecialchars(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>❌ Found " . count($admins_without_profiles) . " admin user(s) without profiles:</p>";
    
    foreach ($admins_without_profiles as $admin) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Email:</strong> " . htmlspecialchars($admin['email']) . "<br>";
        echo "<strong>User ID:</strong> " . $admin['user_id'] . "<br>";
        
        // Create a basic admin profile
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admin_profiles (user_id, first_name, last_name) 
                VALUES (?, 'Admin', 'User')
            ");
            $stmt->execute([$admin['user_id']]);
            echo "<span style='color: green;'>✅ Created basic admin profile (Admin User)</span>";
        } catch (Exception $e) {
            echo "<span style='color: red;'>❌ Failed to create profile: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
        echo "</div>";
    }
}

echo "<br><p><a href='manageuser.php'>← Back to User Management</a></p>";
?>