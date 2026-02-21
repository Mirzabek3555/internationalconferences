<?php
$host = 'localhost';
$db = 'artiqle';
$user = 'root';
$pass = ''; // Try empty. User might have password? .env showed empty.
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully\n";
    // Try simple query
    $stmt = $pdo->query("SELECT 1");
    echo "Query executed: " . $stmt->fetchColumn() . "\n";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
