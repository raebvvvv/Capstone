<?php
// Test Search Functionality - Quick verification script
require_once 'config.php';

echo "<h2>Search Functionality Test</h2>";

try {
    // Test 1: Check if profile tables have data
    echo "<h3>1. Profile Tables Data Check</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as student_count FROM student_profiles");
    $student_count = $stmt->fetch()['student_count'];
    echo "Student profiles: {$student_count}<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as employee_count FROM employee_profiles");
    $employee_count = $stmt->fetch()['employee_count'];
    echo "Employee profiles: {$employee_count}<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as admin_count FROM admin_profiles");
    $admin_count = $stmt->fetch()['admin_count'];
    echo "Admin profiles: {$admin_count}<br>";
    
    // Test 2: Test name queries similar to our updated pages
    echo "<h3>2. Name Query Test (like ticket.php and manageuser.php)</h3>";
    
    $sql = "SELECT u.user_id, u.email, u.role,
            CASE 
                WHEN u.role = 'student' THEN CONCAT(sp.first_name, ' ', sp.last_name)
                WHEN u.role = 'employee' THEN CONCAT(ep.first_name, ' ', ep.last_name)
                WHEN u.role = 'admin' THEN ap.name
                ELSE u.email
            END as full_name
            FROM users u
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
            LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
            LIMIT 5";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>User ID</th><th>Email</th><th>Role</th><th>Full Name</th></tr>";
    
    foreach ($results as $user) {
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['full_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 3: Test submissions with names (like completed_applications.php)
    echo "<h3>3. Submissions with Names Test</h3>";
    
    $sql = "SELECT s.request_id, s.document_title, s.submission_date, u.role,
            CASE 
                WHEN u.role = 'student' THEN CONCAT(sp.first_name, ' ', sp.last_name)
                WHEN u.role = 'employee' THEN CONCAT(ep.first_name, ' ', ep.last_name)
                ELSE u.email
            END as full_name
            FROM submissions s
            LEFT JOIN users u ON s.user_id = u.user_id
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
            WHERE s.status = 'completed'
            LIMIT 5";
    
    $stmt = $pdo->query($sql);
    $submissions = $stmt->fetchAll();
    
    if ($submissions) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Request ID</th><th>Title</th><th>Date</th><th>Role</th><th>Submitter Name</th></tr>";
        
        foreach ($submissions as $sub) {
            echo "<tr>";
            echo "<td>{$sub['request_id']}</td>";
            echo "<td>{$sub['document_title']}</td>";
            echo "<td>{$sub['submission_date']}</td>";
            echo "<td>{$sub['role']}</td>";
            echo "<td>{$sub['full_name']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No completed submissions found.<br>";
    }
    
    echo "<h3>✅ Search Functionality Test Complete</h3>";
    echo "If you see proper names displayed above, the search functionality should work correctly!<br>";
    echo "<p><a href='admin/'>Go to Admin Panel</a> to test search features manually.</p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>