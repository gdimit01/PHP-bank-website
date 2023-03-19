<!DOCTYPE html>
<html>

<head>
    <title>Index</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>

    <?php
    session_start();
    require('dbconnect.php');

    // check if name and customer ID are set in the URL parameters
    if (isset($_GET['name']) && isset($_GET['cid'])) {
        $name = $_GET['name'];
        $cid = $_GET['cid'];

        // Check if user is logged in
        if (!isset($_SESSION['CID'])) {
            header("Location: index.php");
            exit();
        }

        // Check if submitted customer ID matches session value
        if ($cid != $_SESSION['CID']) {
            header("Location: index.php?error=invalid_cid");
            exit();
        }

        try {
            // Query for customer
            $stmt = $conn->prepare("SELECT * FROM Customers WHERE Name = ? AND CID = ?");
            $stmt->execute([$name, $cid]);
            $customer = $stmt->fetch();

            if (!$customer) {
                throw new Exception("Customer ID does not match with the Name.");
            }

            // Query for accounts
            $stmt = $conn->prepare("SELECT * FROM Accounts JOIN Jurisdictions ON Accounts.JID = Jurisdictions.JID WHERE Name = ? AND CID = ?");
            $stmt->execute([$name, $cid]);
            $accounts = $stmt->fetchAll();
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>
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
            <td><?php echo $account['ACID']; ?></td>
            <td><?php echo $account['Name']; ?></td>
            <td><?php echo $account['Balance']; ?></td>
            <td><?php echo $account['Rate']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>Please enter your name and customer ID</p>
    <?php endif; ?>
    <div class="button-group">

        <form action="accounts.php" method="GET" onsubmit="return validate()">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="Sally"><br><br>
            <label for="cid">Customer ID:</label>
            <input type="text" id="cid" name="cid" value="2" required oninput="check(this)"><br>
            <!-- <span id="cid-error" style="display: none; color: red;">Please only use numbers!</span><br><br> -->
            <input type="submit" value="Submit">
        </form>

        <script>
        function validate() {
            const cidInput = document.getElementById("cid");
            const cidError = document.getElementById("cid-error");
            const cidPattern = /^[a-zA-Z-_@.]+$/;
            if (!cidPattern.test(cidInput.value)) {
                cidError.style.display = "inline";
                cidError.textContent = "Please only use numbers!";
                return false;
            } else {
                cidError.style.display = "none";
                return true;
            }
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