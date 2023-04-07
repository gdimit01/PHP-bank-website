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

<body>
    <!-- This is checking if the message is set and if it is then it is displaying the message, the
    *image, the account button, the purchase more button and the exit button. */ -->
    <?php
    if (isset($message)) {
        echo '<h1>Banking COMP8870</h1>';
        echo '<h2>' . $message . '</h2>';
        echo '<img src="images/thanks.png" alt="thanks" width="200" height="200">';
        echo '<form action="accounts.php" method="POST">';
        echo '<input type="hidden" name="name" value="' . urlencode($name) . '">';
        echo '<input type="hidden" name="cid" value="' . $cid . '">';
        echo '<input type="submit" value="Account">';
        echo '</form>';
        echo '<form action="new.php" method="POST">';
        echo '<input type="hidden" name="purchase more">';
        echo '<input type="submit" value="Purchase More">';
        echo '</form>';
        echo '<div class="button-group">';
        echo '<form action="session_destroy.php" method="POST" id="exit-form" onsubmit="return confirm(\'Are you sure you want to exit?\')">';
        echo '<input type="hidden" name="exit">';
        echo '<input class="button" type="submit" value="Exit">';
        echo '</form>';
        echo '</div>';
    }
    ?>
</body>

</html>