<?php
foreach ($products as $product) {
    $profit = $product['selling_price'] - $product['purchase_price'];
    echo "Product: " . $product['product_name'] . " | Profit: " . $profit . "<br>";
}
?>
