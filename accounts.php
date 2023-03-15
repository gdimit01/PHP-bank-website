<head>
    <title>Accounts</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php //define connection once and you don't have to call it again
session_start();
$name = "Sally"; // default value for name
$cid = $_SESSION['CID']; //assuming the customer id is stored in a session variable named CID
require('dbconnect.php');

if (isset($_GET['name']) && isset($_GET['cid'])) {
    $name = $_GET['name'];
    $cid = $_GET['cid'];
    $conn = connect();

    try {
        // Check that the CID matches with the Name
        $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = :cid");
        $stmt->bindValue(':cid', $cid);
        $stmt->execute();
        $customer = $stmt->fetch();

        if (!$customer || $customer['Name'] !== $name) {
            throw new Exception("Customer ID does not match with the Name.");
        }

        $_SESSION['CID'] = $cid;

        // Query for accounts
        $stmt = $conn->prepare("SELECT ACID, Name, Balance, Rate FROM Accounts INNER JOIN Products ON Accounts.PID = Products.PID WHERE CID = :cid");
        $stmt->bindValue(':cid', $cid);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //displaying the results in a table
        echo "<h1>Banking COMP8870</h1>";
        echo "<h2>Hi " . $name . "! Here is a summary of your accounts:</h2>";
        echo "<table>";
        echo "<tr><th>Account Number</th><th>Product</th><th>Balance</th><th>Rate</th><th>Action</th></tr>";
        foreach ($result as $row) {
            echo "<tr data-acid='{$row['ACID']}'><td>" . $row['ACID'] . "</td><td>" . $row['Name'] . "</td><td>" . $row['Balance'] . " USD</td><td>" . $row['Rate'] . "</td><td><button class='delete-btn'>Delete</button></td></tr>";
        }
        echo "</table>";

        $conn = null;
    } catch (PDOException $e) {
        echo "PDOException: " . $e->getMessage();
    } catch (Exception $e) {
        header('Location: errorpg.php?message=' . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!-- adding buttons for navigation -->
<div class="button-group">
    <form action="new.php" method="POST">
        <input type="hidden" name="new">
        <input type="submit" value="New">
    </form>

    <form action="index.php" method="POST">
        <input type="hidden" name="exit">
        <input type="submit" value="Exit">
    </form>
</div>
<!-- add AJAX script for deleting rows -->
<script>
const deleteButtons = document.querySelectorAll('.delete-btn');

deleteButtons.forEach(deleteButton => {
    deleteButton.addEventListener('click', async () => {
        const row = deleteButton.parentNode.parentNode;
        const acid = row.dataset.acid;
        const confirmed = confirm('Are you sure you want to delete this account?');

        if (confirmed) {
            const response = await fetch('delete.php', {
                method: 'POST',
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded'
                },
                body: `acid=${acid}`,
            });

            if (response.ok) {
                row.remove();
                alert('Account successfully deleted!');
            } else {
                alert('Failed to delete account');
            }
        }
    });
});
</script>