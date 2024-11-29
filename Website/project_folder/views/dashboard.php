<?php
// Include the database connection and start session
include('../includes/db.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details
$userId = $_SESSION['user_id'];
try {
    $userQuery = $pdo->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $userQuery->execute([$userId]);
    $user = $userQuery->fetch();
    
    if (!$user) {
        throw new Exception("User not found.");
    }

    // Fetch total orders placed by the user
    $ordersQuery = $pdo->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?");
    $ordersQuery->execute([$userId]);
    $totalOrders = $ordersQuery->fetch()['total_orders'];

    // Fetch total products in stock for display
    $totalProductsQuery = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
    $totalProducts = $totalProductsQuery->fetch()['total_products'];

    // Fetch total amount spent by the user in all orders
    $totalAmountQuery = $pdo->prepare("SELECT SUM(total_price) AS total_amount FROM orders WHERE user_id = ?");
    $totalAmountQuery->execute([$userId]);
    $totalAmount = $totalAmountQuery->fetch()['total_amount'];
    $totalAmount = $totalAmount ? $totalAmount : 0; // If no orders, set to 0
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .user-info, .stats, .actions {
            margin: 20px 0;
            padding: 15px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .user-info h2, .stats h2, .actions h2 {
            margin: 0 0 10px;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .stat-box {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #e6f7ff;
        }
        .stat-box h3 {
            margin: 5px 0;
            color: #007bff;
        }
        .actions a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .logout {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #dc3545;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .logout:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h1>Welcome to Your Dashboard</h1>

    <!-- User Information -->
    <div class="user-info">
        <h2>Your Information</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-box">
            <h3><?php echo $totalOrders; ?></h3>
            <p>Your Total Orders</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $totalProducts; ?></h3>
            <p>Available Products</p>
        </div>
        <div class="stat-box">
            <h3><?php echo number_format($totalAmount, 2); ?> BDT</h3>
            <p>Total Spent</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <h2>Quick Actions</h2>
        <a href="order.php">Place a New Order</a>
    </div>

    <!-- Logout Button -->
    <a href="logout.php" class="logout">Logout</a>
</div>

</body>
</html>

