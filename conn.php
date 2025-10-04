<?php
$host = 'localhost';
$db   = 'ipmo_users'; // your database name
$user = 'root';       // your database username
$pass = '';           // your database password
$charset = 'utf8mb4';
$port='3306'; // your database port, default is usually 3306

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
?>
