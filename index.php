<?php
session_start();
require('dbconnect.php');

if (isset($_POST['name']) && isset($_POST['cid'])) {
    $name = $_POST['name'];
    $cid = $_POST['cid'];

    try {
        $stmt = $conn->prepare("SELECT * FROM Customers WHERE Name = ? AND CID = ?");
        $stmt->execute([$name, $cid]);
        $customer = $stmt->fetch();

        $stmt = $conn->prepare("SELECT Accounts.*, Jurisdictions.Capital FROM Accounts JOIN Jurisdictions ON Accounts.JID = Jurisdictions.JID WHERE CID = ?");
        $stmt->execute([$cid]);
        $accounts = $stmt->fetchAll();
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Index</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Banking COMP8870</h1>
    <img src="images/computer.png" alt="computer" width="200" height="200">
    <?php if (isset($accounts) && count($accounts) > 0): ?>
    <table>
        <tr>
            <th>Account Number</th>
            <th>Product</th>
            <th>Balance</th>
            <th>Rate</th>
        </tr>
        <?php foreach ($accounts as $account): ?>
        <tr>
            <td><?php echo htmlspecialchars($account['ACID']); ?></td>
            <td><?php echo htmlspecialchars($account['Name']); ?></td>
            <td><?php echo htmlspecialchars($account['Balance']); ?></td>
            <td><?php echo htmlspecialchars($account['Rate']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>Please enter your name and customer ID</p>
    <?php endif; ?>

    <div class="button-group">
        <form action="accounts.php" method="POST" onsubmit="return validate()">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="Sally"><br><br>
            <label for="cid">Customer ID:</label>
            <input type="text" id="cid" name="cid" value="2" required oninput="check(this)"><br>
            <input type="submit" value="Submit">
        </form>
    </div>

    <script>
    function validate() {
        const cidInput = document.getElementById('cid');
        const cidPattern = /^[0-9]+$/;
        if (!cidPattern.test(cidInput.value)) {
            alert('Please only use numbers for the Customer ID.');
            return false;
        }
        return true;
    }

    function check(input) {
        if (isNaN(input.value)) {
            input.setCustomValidity('"' + input.value + '" is not a number.');
        } else {
            input.setCustomValidity('');
        }
    }
    </script>
</body>

</html>