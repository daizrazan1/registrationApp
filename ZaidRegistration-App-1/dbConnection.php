<?php

/**
 * Database Connection Setup (MySQL PDO)
 * Establishes a connection to the remote MySQL database
 */

$host = 'srv941.hstgr.io'; 
$db   = 'u237055794_Zaid';
$user = 'u237055794_388620';
$pass = '~7Zs&M$^WNT';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

return $pdo;

?>
