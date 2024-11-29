<?php
// Include database connection and start session
include('../includes/db.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch available products
try {
    $productsQuery = $pdo->query("SELECT product_id, product_name, selling_price, stock_quantity FROM products");
    $products = $productsQuery->fetchAll();
} catch (PDOException $e) {
    die('Failed to fetch products: ' . $e->getMessage());
}

// Handle order form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    try {
        // Fetch product details
        $productQuery = $pdo->prepare("SELECT selling_price, stock_quantity FROM products WHERE product_id = ?");
        $productQuery->execute([$productId]);
        $product = $productQuery->fetch();

        if (!$product || $quantity > $product['stock_quantity']) {
            $error = "Invalid product or insufficient stock.";
        } else {
            // Calculate total price
            $totalPrice = $product['selling_price'] * $quantity;

            // Insert into orders table
            $pdo->beginTransaction();
            $insertOrderQuery = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
            $insertOrderQuery->execute([$userId, $totalPrice]);
            $orderId = $pdo->lastInsertId();

            // Insert into order_items table
            $insertOrderItemQuery = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $insertOrderItemQuery->execute([$orderId, $productId, $quantity, $totalPrice]);

            // Update product stock
            $updateStockQuery = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $updateStockQuery->execute([$quantity, $productId]);

            $pdo->commit();
            $success = "Order placed successfully!";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Failed to place order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    <script>
        function updateTotalPrice() {
            var productId = document.getElementById("product_id").value;
            var quantity = document.getElementById("quantity").value;
            var totalPriceElement = document.getElementById("total_price");

            // Get the selling price of the selected product
            var products = <?php echo json_encode($products); ?>;
            var selectedProduct = products.find(function(product) {
                return product.product_id == productId;
            });

            if (selectedProduct && quantity > 0) {
                var totalPrice = selectedProduct.selling_price * quantity;
                totalPriceElement.innerHTML = "Total Price: $" + totalPrice.toFixed(2);
            } else {
                totalPriceElement.innerHTML = "Total Price: $0.00";
            }
        }
    </script>
</head>
<body>

<h1>Place Order</h1>

<?php if (!empty($success)): ?>
    <div class="message success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="product_id">Select Product:</label>
        <select name="product_id" id="product_id" required onchange="updateTotalPrice()">
            <option value="">-- Select a Product --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name'] . " - $" . number_format($product['selling_price'], 2) . " (Stock: " . $product['stock_quantity'] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required onchange="updateTotalPrice()">
    </div>
    <div class="form-group">
        <p id="total_price">Total Price: $0.00</p>
    </div>
    <div class="form-group">
        <button type="submit">Place Order</button>
    </div>
</form>

</body>
</html>
