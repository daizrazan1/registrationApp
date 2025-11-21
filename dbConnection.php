<?php

/**
 * Database Connection Setup (PDO)
 * * This file establishes a secure connection to the MySQL database
 * using the credentials provided by the hosting environment.
 * The function returns a PDO object for querying the database.
 */

// Your database credentials
$host = 'srv941.hstgr.io'; 
$db   = 'u237055794_Zaid';
$user = 'u237055794_388620';
$pass = '~7Zs&M$^WNT'; // NOTE: In a real application, never store credentials directly in version control.

// The DSN (Data Source Name) string
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    // Throw exceptions on error for better error handling
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Fetch results as an associative array by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disable emulation of prepared statements
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Create a new PDO instance (the connection)
     $pdo = new PDO($dsn, $user, $pass, $options);

     // Optional: You can remove this line after testing
     // echo "Database connection successful!<br>"; 

} catch (\PDOException $e) {
     // Handle connection errors gracefully
     // We use 'getMessage()' only for debugging; for security, a generic message is better.
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Return the PDO object for use in other files
return $pdo;

?>