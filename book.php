<?php
session_start();
require('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['cid'])) { // Ensure this matches how it's set in `new.php`.
        header('Location: errorpg.php?message=' . urlencode("Please log in to buy a product"));
        exit();
    }

    $cid = $_SESSION['cid']; // Use lowercase as set in `new.php`.
    $product = $_POST['product'];

    try {
        $conn = connect();
        if (!$conn) {
            throw new Exception("Connection failed.");
        }

        $stmt = $conn->prepare("INSERT INTO Accounts (Balance, CID, PID) VALUES (0, :cid, :product)");
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':product', $product, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = ?");
        $stmt->execute([$cid]);
        $customer = $stmt->fetch();

        if (!$customer) {
            throw new Exception("Customer not found.");
        }

        $name = $customer['Name']; // Fetch the customer's name for the thank you message.
        $message = "Thank you " . htmlspecialchars($name) . ", your purchase was successful!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Buy Product</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php if (isset($message)): ?>
    <h1>Banking COMP8870</h1>
    <h2><?= $message ?></h2>
    <img src="images/thanks.png" alt="Thanks" width="200" height="200">
    <form action="accounts.php" method="POST">
        <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
        <input type="hidden" name="cid" value="<?= htmlspecialchars($cid) ?>">
        <input type="submit" value="Account">
    </form>
    <form action="new.php" method="POST">
        <input type="hidden" name="purchase more">
        <input type="submit" value="Purchase More">
    </form>
    <div class="button-group">
        <form action="session_destroy.php" method="POST" id="exit-form"
            onsubmit="return confirm('Are you sure you want to exit?')">
            <input type="hidden" name="exit">
            <input class="button" type="submit" value="Exit">
        </form>
    </div>
    <?php endif; ?>
</body>

</html>