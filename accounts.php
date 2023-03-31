<head>
    <title>Accounts</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php //define connection once and you don't have to call it again
session_start();

  
    $name = isset($_POST['name']) ? $_POST['name'] : '?';
    $cid = isset($_SESSION['CID']) ? $_SESSION['CID'] : '?';

    require('dbconnect.php');

/* This is checking if the name and cid are set. If they are, it will set the name and cid to the
    post name and cid. Then it will connect to the database. Then it will check if the cid matches
    the name. If it doesn't, it will throw an exception. If it does, it will set the session cid to
    the cid. */
   if (isset($_POST['name']) && isset($_POST['cid'])) {
    $_SESSION['cid'] = $_POST['cid'];
    $_SESSION['name'] = $_POST['name'];
    header('Location: accounts.php');
    }

    /* This is checking if the cid and name are set. If they are, it will set the cid and name to the
    session cid and name. If they aren't, it will redirect to the error page. */
    if (isset($_SESSION['cid']) && isset($_SESSION['name'])) {
        $cid = $_SESSION['cid'];
        $name = $_SESSION['name'];
    } else {
        header('Location: errorpg.php');
    }
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