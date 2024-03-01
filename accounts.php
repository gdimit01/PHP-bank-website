<?php
// Enable error reporting for debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require('dbconnect.php');

$conn = connect();

// Redirect if the database connection fails.
if (!$conn) {
    header('Location: errorpg.php?message=' . urlencode("Connection failed: could not connect to the database"));
    exit();
}

// Check if user details are posted and set session variables.
if (isset($_POST['name']) && isset($_POST['cid'])) {
    $_SESSION['cid'] = $_POST['cid'];
    $_SESSION['name'] = $_POST['name'];
    header('Location: accounts.php');
    exit();
}

// Ensure the user is logged in before proceeding.
if (!isset($_SESSION['cid']) || !isset($_SESSION['name'])) {
    header('Location: errorpg.php?message=' . urlencode("Please log in to view accounts"));
    exit();
}

$cid = $_SESSION['cid'];
$name = $_SESSION['name'];

try {
    // Validate the logged-in customer.
    $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = :cid");
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch();
    if (!$customer || $customer['Name'] !== $name) {
        throw new Exception("Customer ID does not match with the Name.");
    }

    // Fetch associated accounts and products.
    $stmt = $conn->prepare("SELECT Accounts.ACID, Products.Name, Accounts.Balance, Products.Rate FROM Accounts INNER JOIN Products ON Accounts.PID = Products.PID WHERE Accounts.CID = :cid");
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: errorpg.php?message=' . urlencode("PDOException: " . $e->getMessage()));
    exit();
} catch (Exception $e) {
    header('Location: errorpg.php?message=' . urlencode($e->getMessage()));
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Accounts</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php if (!empty($accounts)): ?>
    <table>
        <tr>
            <th>Account Number</th>
            <th>Product</th>
            <th>Balance</th>
            <th>Rate</th>
            <th>Action</th>
        </tr>
        <?php foreach ($accounts as $row): ?>
        <tr id="account-row-<?php echo htmlspecialchars($row['ACID']); ?>">
            <td><?php echo htmlspecialchars($row['ACID']); ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Balance']) . " " . htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Rate']); ?></td>
            <td><button class="delete-btn" data-acid="<?php echo htmlspecialchars($row['ACID']); ?>">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No accounts found for the given details.</p>
    <?php endif; ?>

    <div class="button-group">
        <form action="new.php" method="POST">
            <input type="hidden" name="new">
            <input type="submit" value="New">
        </form>
        <form action="session_destroy.php" method="POST" id="exit-form"
            onsubmit="return confirm('Are you sure you want to exit?')">
            <input type="hidden" name="exit">
            <input class="button" type="submit" value="Exit">
        </form>
    </div>

    <script>
    document.querySelectorAll('.delete-btn').forEach(deleteButton => {
        deleteButton.addEventListener('click', function() {
            const acid = this.dataset.acid;
            const confirmed = confirm('Are you sure you want to delete this account?');
            if (confirmed) {
                fetch('delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `acid=${acid}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        if (data.trim().includes("success")) {
                            alert('Account successfully deleted!');
                            const row = document.getElementById('account-row-' + acid);
                            if (row) {
                                row.remove();
                            }
                        } else {
                            alert(`Failed to delete account. ${data.trim()}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        });
    });
    </script>
</body>

</html>