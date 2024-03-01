<?php
session_start();
require('dbconnect.php');

// Redirect if CID or name are not set in the session
if (!isset($_SESSION['cid']) || !isset($_SESSION['name'])) {
    header('Location: errorpg.php?message=' . urlencode("Please log in to continue"));
    exit();
}

$cid = $_SESSION['cid'];
$name = $_SESSION['name'];

try {
    $conn = connect();
    if (!$conn) {
        throw new Exception("Connection failed: could not connect to database");
    }

    // Fetch the name of the logged-in customer
    $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = ?");
    $stmt->execute([$cid]);
    $customer = $stmt->fetch();
    if (!$customer) {
        throw new Exception("Customer not found");
    }
    $name = $customer['Name']; // Update the name with the fetched value

    // Query for products
    $sql = "SELECT * FROM Products";
    $handle = $conn->prepare($sql);
    $handle->execute();
    $result = $handle->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "PDOException: " . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>New</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Banking COMP8870</h1>
    <h2>Dear <?= htmlspecialchars($name) ?>, please select a product:</h2>
    <form action='book.php' method='POST'>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Rate</th>
                <th>Select</th>
            </tr>
            <?php foreach ($result as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['PID']) ?></td>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['Rate']) ?></td>
                <td><input type='radio' name='product' value='<?= htmlspecialchars($row['PID']) ?>' required></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php include('currency.php'); ?>
        <br>
        <?php include('currency_script.php'); ?>
        <?php include('buttons_new.php'); ?>
    </form>
</body>

</html>