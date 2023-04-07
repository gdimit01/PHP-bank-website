<head>
    <title>buy product</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<?php
session_start();

/* This is checking if the CID and Name are set in the post and if they are then it is setting the
session CID and Name to the post CID and Name and then redirecting the user to the accounts page. */
if (isset($_POST['CID']) && isset($_POST['Name'])){
    $_SESSION['CID'] = $_POST['CID'];
    $_SESSION['Name'] = $_POST['Name'];
    header('Location: accounts.php');
    exit();
}

/* This is checking if the CID is set in the session and if it is not then it is redirecting the user
to the error page with the message "Please log in to buy a product". */
if (!isset($_SESSION['CID'])) {
    header('Location: errorpg.php?message=Please%20log%20in%20to%20buy%20a%20product');
    exit();
}

/* This is checking if the request method is POST and if it is then it is setting the CID and product
to the session CID and the product that was posted. */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cid = $_SESSION['CID'];
    $product = $_POST['product'];

    // insert new account for the current user
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

        // prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO Accounts(Balance, CID, PID) VALUES ('0', :cid, :product)");
        $stmt->bindParam(':cid', $cid);
        $stmt->bindParam(':product', $product);
        $stmt->execute();

        // Get the name of the customer who made the purchase
        $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = ?");
        $stmt->execute([$cid]);
        $customer = $stmt->fetch();
        $name = $customer['Name'];

        // Set the success message with the customer's name
        $message = "Thank you " . $name . ", your booking was successful!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    } finally {
        $conn = null;
    }
    
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Buy Product</title>
</head>

<body>
    <?php if (isset($message)) : ?>
    <h1>Banking COMP8870</h1>
    <h2><?php echo $message; ?></h2>
    <!-- Include the image when the purchase is successful -->
    <img src="images/thanks.png" alt="thanks" width="200" height="200">
    <form action="accounts.php" method="GET">
        <input type="hidden" name="name" value="<?php echo urlencode($name); ?>">
        <input type="hidden" name="cid" value="<?php echo $cid; ?>">
        <input type="submit" value="Account">
    </form>

    <form action="new.php" method="POST">
        <input type="hidden" name="purchase more">
        <input type="submit" value="Purchase More">
    </form>
    <div class='button-group'>
        <form action='index.php' method='POST'>
            <input type='hidden' name='exit'>
            <input class='button' type='submit' value='Exit'>
        </form>
    </div>

    <?php else : ?>
    <h1>Buy Product</h1>

    <form action="book.php" method="POST">
        <input type="radio" id="product1" name="product" value="1">
        <label for="product1">Product 1</label><br>
        <input type="radio" id="product2" name="product" value="2">
        <label for="product2">Product 2</label><br>
        <button type="submit">Buy</button>
    </form>
    <?php endif; ?>
</body>

</html>