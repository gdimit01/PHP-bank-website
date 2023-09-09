<?php 
function connect() { 
    $host = 'localhost'; 
    $dbname = 'db_bank'; 
    $user = 'php'; 
    $pwd = 'xxx';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4"; // Added charset for better character support
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
        PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
    ];

    try { 
        $conn = new PDO($dsn, $user, $pwd, $options);
        return $conn; 
    } catch (PDOException $e) { 
        error_log("PDOException: " . $e->getMessage()); // Log the error instead of echoing it
        return null;
    } 
} 
?>