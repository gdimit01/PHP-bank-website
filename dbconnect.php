<?php 
function connect() { 
    $host = 'dragon.ukc.ac.uk'; 
    $dbname = 'gd353'; 
    $user = 'gd353'; 
    $pwd = 'o2ormus';

    $dsn = "mysql:host=$host;dbname=$dbname";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    try { 
        $conn = new PDO($dsn, $user, $pwd, $options); // use $dsn instead of the connection string
        return $conn; 
    } catch (PDOException $e) { 
        echo "PDOException: ".$e->getMessage(); 
        return null;
    } 
} 
?>