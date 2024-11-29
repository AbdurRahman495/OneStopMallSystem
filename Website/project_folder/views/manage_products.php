<?php
// Include the database connection file
include('../includes/db.php'); // Ensure this path is correct and `db.php` has the correct PDO connection setup

try {
    // SQL query to fetch all products
    $sql = "SELECT product_id, product_name, selling_price, stock_quantity FROM products";
    $stmt = $pdo->query($sql);

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if products exist
    if (empty($products)) {
        echo "<p>No products found in the database.</p>";
    } else {
        // Display product details in a structured table
        echo "<h1>Product List</h1>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<thead>";
        echo "<tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Selling Price</th>
                <th>Stock Quantity</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";

        // Loop through products and display their details
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product['product_id']) . "</td>";
            echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
            echo "<td>" . number_format(htmlspecialchars($product['selling_price']), 2) . "</td>";
            echo "<td>" . htmlspecialchars($product['stock_quantity']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }
} catch (Exception $e) {
    // Handle errors (e.g., database connection issues, query problems)
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
