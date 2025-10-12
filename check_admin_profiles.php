<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';
if (empty($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
    http_response_code(403);
    echo 'Forbidden: Admin access required';
    exit;
}

echo "<h2>Database Table Structure Check</h2>";

try {
    // Check admin_profiles structure
    echo "<h3>admin_profiles table structure:</h3>";
    $stmt = $pdo->query('DESCRIBE admin_profiles');
    $columns = $stmt->fetchAll();
    
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Check if there are any admin records
    echo "<h3>Sample admin_profiles data:</h3>";
    $stmt = $pdo->query('SELECT * FROM admin_profiles LIMIT 3');
    $admins = $stmt->fetchAll();
    
    if ($admins) {
        echo "<table border='1'>";
        // Header row
        echo "<tr>";
        foreach (array_keys($admins[0]) as $key) {
            if (!is_numeric($key)) {
                echo "<th>{$key}</th>";
            }
        }
        echo "</tr>";
        
        // Data rows
        foreach ($admins as $admin) {
            echo "<tr>";
            foreach ($admin as $key => $value) {
                if (!is_numeric($key)) {
                    echo "<td>{$value}</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No admin profiles found.";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>