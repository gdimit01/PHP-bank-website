<?php 
function connect() { 
    $host = 'db4free.net'; 
    $dbname = 'db_bank'; 
    $user = 'php_bank'; 
    $pwd = 'xxxxxxxx';
    $port = '3306'; // Specify the port number

    // Include the port number in the DSN
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // It's good practice to set a default fetch mode.
        PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation of prepared statements, use real prepared statements instead.
    ];

    try { 
        $conn = new PDO($dsn, $user, $pwd, $options);
        echo "Connected successfully"; // It's better to handle success outside of your connection function.
        return $conn; 
    } catch (PDOException $e) { 
        echo "PDOException: ".$e->getMessage(); 
        return null; // It's better to throw the exception or handle it appropriately.
    } 
}
?>