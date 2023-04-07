<!-- /* A PHP script that is creating a radio button group. */ -->
<div class="currency-container">
    <?php
    $currencies = [
        ['name' => 'Any', 'id' => 'any', 'onchange' => 'handleCurrencyChange(false)', 'required' => true],
        ['name' => 'USD', 'id' => 'usd', 'onchange' => 'handleCurrencyChange()', 'required' => true],
        ['name' => 'GBP', 'id' => 'gbp', 'onchange' => 'handleCurrencyChange()', 'required' => true],
        ['name' => 'EUR', 'id' => 'eur', 'onchange' => 'handleCurrencyChange()', 'required' => true]
    ];

    foreach ($currencies as $currency) {
        echo '<div class="currency-option">';
        echo '<label for="' . $currency['id'] . '">' . $currency['name'] . '</label>';
        echo '<input type="radio" name="currency" value="' . $currency['name'] . '" id="' . $currency['id'] . '"';
        echo ' onchange="' . $currency['onchange'] . '"';
        echo $currency['required'] ? ' required' : '';
        echo '>';
        echo '</div>';
    }
    ?>
</div>