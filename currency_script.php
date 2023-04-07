<!-- Add currency radio button event listener -->
<script>
const products = document.querySelectorAll('input[name="product"]');
const currencies = document.querySelectorAll('input[name="currency"]');

/**
 * If the product is disabled, prevent the default action and uncheck the product.
 */
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

/**
 * This function enables a product.
 */
function enableProduct(product) {
    product.disabled = false;
}

/**
 * For each product in the products array, call the disableProduct function with the product as the
 * argument.
 */
function disableAllProducts() {
    products.forEach(disableProduct);
}

/**
 * For each product in the products array, call the enableProduct function.
 */
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

/* Adding an event listener to each currency radio button. When the currency radio button is changed,
the handleCurrencyChange function is called. */
currencies.forEach(currency => {
    currency.addEventListener('change', handleCurrencyChange);
});
</script>