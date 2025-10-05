<?php
// Quick test to verify users exist and can be searched
require_once '../config.php';

echo "<h2>Manage Users Search Test</h2>";

try {
    // Test 1: Check if we have any users at all
    echo "<h3>1. Total Users Count</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $stmt->fetch()['total'];
    echo "Total users in database: {$total}<br><br>";
    
    if ($total == 0) {
        echo "<strong style='color: red;'>No users found! This is why search returns empty results.</strong><br>";
        echo "You need to register some users first.<br><br>";
    }
    
    // Test 2: Sample of users with their profile data
    echo "<h3>2. Sample Users with Profile Data</h3>";
    $sql = "SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status,
        CASE 
            WHEN u.role = 'student' THEN sp.student_number
            WHEN u.role = 'employee' THEN ep.employee_number  
            WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
            ELSE CONCAT('User-', u.user_id)
        END as identifier,
        CASE 
            WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
            WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
            WHEN u.role = 'admin' THEN CONCAT_WS(' ', ap.first_name, ap.last_name)
            ELSE 'Unknown User'
        END as full_name
    FROM users u
    LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
    LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
    LIMIT 10";
    
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Status</th><th>Identifier</th><th>Full Name</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "<td>{$user['identifier']}</td>";
            echo "<td>{$user['full_name']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "No users found.<br><br>";
    }
    
    // Test 3: Test search functionality with sample search
    if ($total > 0) {
        echo "<h3>3. Test Search for 'Juan'</h3>";
        $search_param = '%Juan%';
        
        $sql_test = "SELECT 
            u.user_id, 
            u.email, 
            u.role, 
            u.status,
            CASE 
                WHEN u.role = 'student' THEN sp.student_number
                WHEN u.role = 'employee' THEN ep.employee_number  
                WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
                ELSE CONCAT('User-', u.user_id)
            END as identifier,
            CASE 
                WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
                WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
                WHEN u.role = 'admin' THEN CONCAT_WS(' ', ap.first_name, ap.last_name)
                ELSE 'Unknown User'
            END as full_name
        FROM users u
        LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
        LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
        LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
        WHERE (
            u.email LIKE ? 
            OR sp.student_number LIKE ?
            OR ep.employee_number LIKE ?
            OR CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name) LIKE ?
            OR CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name) LIKE ?
            OR CONCAT_WS(' ', ap.first_name, ap.last_name) LIKE ?
            OR sp.first_name LIKE ?
            OR sp.last_name LIKE ?
            OR ep.first_name LIKE ?
            OR ep.last_name LIKE ?
            OR ap.first_name LIKE ?
            OR ap.last_name LIKE ?
        )";
        
        $stmt_test = $pdo->prepare($sql_test);
        for ($i = 1; $i <= 12; $i++) {
            $stmt_test->bindValue($i, $search_param, PDO::PARAM_STR);
        }
        $stmt_test->execute();
        $search_results = $stmt_test->fetchAll();
        
        if ($search_results) {
            echo "Found " . count($search_results) . " results for 'Juan':<br>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Email</th><th>Role</th><th>Identifier</th><th>Full Name</th></tr>";
            
            foreach ($search_results as $user) {
                echo "<tr>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['role']}</td>";
                echo "<td>{$user['identifier']}</td>";
                echo "<td><strong>{$user['full_name']}</strong></td>";
                echo "</tr>";
            }
            echo "</table><br>";
        } else {
            echo "No results found for search term 'Juan'. Try searching for actual names from the user list above.<br><br>";
        }
    }
    
    echo "<h3>✅ Search Test Complete</h3>";
    echo "<p><a href='manageuser.php'>Go to Manage Users page</a> to test the fixed search functionality!</p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>