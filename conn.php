<?php
// Database Connection with Environment Variables
require_once __DIR__ . '/env_config.php';

$host = Environment::get('DB_HOST', 'localhost');
$db   = Environment::get('DB_NAME', 'ipmo_users'); // your database name
$user = Environment::get('DB_USERNAME', 'root');   // your database username
$pass = Environment::get('DB_PASSWORD', '');       // your database password
$charset = 'utf8mb4';
$port = Environment::get('DB_PORT', '3307');       // your database port

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Don't expose connection details in production
    if (Environment::isDevelopment()) {
        exit('Database connection failed: ' . $e->getMessage());
    } else {
        error_log('Database connection failed: ' . $e->getMessage());
        exit('Database connection failed. Please contact administrator.');
    }
}
?>
