<head>
    <title>New</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<?php
session_start();

require('dbconnect.php');

if (isset($_SESSION['CID'])) {
    $cid = $_SESSION['CID'];
} else {
    // handle the case where the CID is not set
}

$conn = connect(); 

try { 
    // Fetch the name of the logged-in customer
    $stmt = $conn->prepare("SELECT Name FROM Customers WHERE CID = 2");
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

    <style>
    .currency-container {
        display: flex;
        flex-wrap: wrap;
    }

    .currency-option {
        margin-right: 10px;
        display: flex;
        align-items: center;
    }

    .currency-option input {
        margin-right: 5px;
    }
    </style>

    <!-- adding currency options -->
    <div class="currency-container">
        <div class="currency-option">
            <label for='any'>Any</label>
            <input type='radio' name='currency' value='Any' id='any' onchange='handleCurrencyChange(false)' required>
        </div>
        <div class="currency-option">
            <label for='usd'>USD</label>
            <input type='radio' name='currency' value='USD' id='usd' onchange='handleCurrencyChange()' required>
        </div>
        <div class="currency-option">
            <label for='gbp'>GBP</label>
            <input type='radio' name='currency' value='GBP' id='gbp' onchange='handleCurrencyChange()' required>
        </div>
        <div class="currency-option">
            <label for='eur'>EUR</label>
            <input type='radio' name='currency' value='EUR' id='eur' onchange='handleCurrencyChange()' required>
        </div>

    </div>
    <br>

    <!-- adding buy button with class name for styling -->
    <div class='button-group'>
        <form action='new.php' method='POST'>
            <input type='hidden' name='book'>
            <input class='button' type='submit' value='Buy Product'>
        </form>
    </div>

    <!-- adding account button with class name for styling -->
    <div class=" button-group">
        <form action="accounts.php" method="GET">
            <input type="hidden" name="cid" value="<?php echo $cid ?>">
            <input type="hidden" name="name" value="<?php echo $name ?>">
            <input class="button" type="submit" value="Accounts">
        </form>
    </div>


    <!-- adding exit button with class name for styling -->
    <div class='button-group'>
        <form action='index.php' method='POST'>
            <input type='hidden' name='exit'>
            <input class='button' type='submit' value='Exit'>
        </form>
    </div>
    <!-- Add currency radio button event listener -->
    <script>
    const products = document.querySelectorAll('input[name="product"]');
    const currencies = document.querySelectorAll('input[name="currency"]');

    function disableProduct(product) {
        product.disabled = true;
        product.checked = false;
        product.addEventListener('click', function(event) {
            if (product.disabled) {
                event.preventDefault();
                product.checked = false;
            }
        });
    }

    function enableProduct(product) {
        product.disabled = false;
    }

    function disableAllProducts() {
        products.forEach(disableProduct);
    }

    function enableAllProducts() {
        products.forEach(enableProduct);
    }

    /**
     * If the selected currency is 'Any', enable all products. Otherwise, enable products that match
     * the selected currency or are in the list of products that are always enabled.
     */
    function handleCurrencyChange() {
        const selectedCurrency = document.querySelector('input[name="currency"]:checked').value;

        if (selectedCurrency === 'Any') {
            enableAllProducts();
        } else {
            products.forEach(product => {
                const productCurrency = product.dataset.currency;
                const productOption = document.getElementById(`product_${product.id}_${selectedCurrency}`);
                const productId = parseInt(product.value);

                if (productCurrency === selectedCurrency && (productOption && productOption.checked)) {
                    enableProduct(product);
                } else if ((selectedCurrency === 'USD' && [1, 2].includes(productId)) ||
                    (selectedCurrency === 'EUR' && productId === 4) ||
                    (selectedCurrency === 'GBP' && productId === 3)) {
                    enableProduct(product);
                } else {
                    disableProduct(product);
                }
            });
        }
    }

    disableAllProducts(); // disable all products initially

    currencies.forEach(currency => {
        currency.addEventListener('change', handleCurrencyChange);
    });
    </script>

    </body>

    </html>
    <?php
} catch (PDOException $e) {
    echo "PDOException: ".$e->getMessage();
}
?>