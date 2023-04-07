<head>
    <title>New</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<?php
session_start();

require('dbconnect.php');

/* This is checking if the CID is set in the session. If it is, it sets the  variable to the CID in
the session. If it is not, it handles the case where the CID is not set. */
if (isset($_POST['name']) && isset($_POST['cid'])) {
    $_SESSION['cid'] = $_POST['cid'];
    $_SESSION['name'] = $_POST['name'];
    header('Location: new.php');
    }

    /* This is checking if the CID and name are set in the session. If they are, it sets the variables
    to the CID and name in the session. If they are not, it handles the case where the CID and name
    are not set. */
    if (isset($_SESSION['cid']) && isset($_SESSION['name'])) {
        $cid = $_SESSION['cid'];
        $name = $_SESSION['name'];
    } else {
        header('Location: errorpg.php?message=An%20error%20has%20occurred');
    }

$conn = connect(); 

try { 
    // Fetch the name of the logged-in customer
    $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = ?");
    $stmt->execute([$cid]);
    $customer = $stmt->fetch();
    

    // Set the value of $name to the customer name
    $name = $customer['Name'];
    
    // Query for products
    $sql = "SELECT * FROM Products"; 
    $handle = $conn->prepare($sql); 
    $handle->execute(); 
    $result = $handle->fetchAll(PDO::FETCH_ASSOC); //fetching all the results from the query


?>
<h1>Banking COMP8870</h1>
<h2>Dear <?= $name ?> please select a product: </h2>
<form action='book.php' method='POST'>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Rate</th>
        </tr>
        <!-- //This is a foreach loop that is iterating through the results of the query. It is creating a
        /table row for each product. */ -->
        <?php foreach($result as $row): ?>
        <tr>
            <td><?= $row['PID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['Rate'] ?></td>
            <td><input type='radio' name='product' value='<?= $row['PID'] ?>' required></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>

    <?php include('currency.php'); ?>

    <br>
    <?php include('currency_script.php'); ?>
    <?php include('buttons_new.php'); ?>

    </body>

    </html>
    <?php
/* This is a try catch block. It is trying to execute the code in the try block. If it fails, it will
catch the error and print it out. */
} catch (PDOException $e) {
    echo "PDOException: ".$e->getMessage();
}
?>