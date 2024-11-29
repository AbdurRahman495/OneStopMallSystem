<?php
// Start the session
session_start();

// Include the database connection
include('../includes/db.php'); // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>You need to log in to view your orders.</p>";
    exit; // Stop script execution if not logged in
}

try {
    // Prepare and execute the query
    $sql = "SELECT * FROM orders WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();

    // Display orders
    echo "<h2>Your Orders</h2>";
    if (count($orders) > 0) {
        foreach ($orders as $order) {
            echo "Order ID: " . htmlspecialchars($order['order_id']) . 
                 " | Total Price: " . htmlspecialchars($order['total_price']) . 
                 " | Status: " . htmlspecialchars($order['status']) . "<br>";
        }
    } else {
        echo "<p>No orders found.</p>";
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "<p>Error fetching orders: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Include the footer
include('../includes/footer.php'); // Ensure this path is correct
?>

