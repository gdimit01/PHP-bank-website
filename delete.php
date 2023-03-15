<?php

if (isset($_POST['acid'])) {
    $acid = $_POST['acid'];

    // Connect to the database
    $host = 'dragon.ukc.ac.uk';
    $dbname = 'gd353';
    $user = 'gd353';
    $pwd = 'o2ormus';
    $dsn = "mysql:host=$host;dbname=$dbname";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    try {
        $conn = new PDO($dsn, $user, $pwd, $options);
    } catch (PDOException $e) {
        echo "PDOException: " . $e->getMessage();
        die();
    }

    // Delete the row from the database
    try {
        $stmt = $conn->prepare("DELETE FROM Accounts WHERE ACID = :acid");
        $stmt->bindValue(':acid', $acid);
        $stmt->execute();
        echo "success";
    } catch (PDOException $e) {
        echo "PDOException: " . $e->getMessage();
        die();
    }

    // Get the maximum account ID from the Accounts table
    $stmt = $conn->query("SELECT MAX(ACID) FROM Accounts");
    $max_acid = $stmt->fetchColumn();

    // Reset the auto-increment value for the Accounts table
    $stmt = $conn->prepare("ALTER TABLE Accounts AUTO_INCREMENT = :max_acid");
    $stmt->bindValue(':max_acid', $max_acid);
    $stmt->execute();

    $conn = null;
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Delete Account</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1><?php echo $message; ?></h1>
    <form action="accounts.php" method="GET">
        <button type="submit">Accounts Page</button>
    </form>
    <form action="index.php" method="POST">
        <input type="hidden" name="exit">
        <button type="submit">Home Page</button>
    </form>
</body>

</html>