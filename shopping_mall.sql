CREATE DATABASE shopping_mall;

USE shopping_mall;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    purchase_price DECIMAL(10, 2) NOT NULL,
    selling_price DECIMAL(10, 2) NOT NULL,
    production_date DATE,
    expiry_date DATE,
    stock_quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order Items Table
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO products (product_name, purchase_price, selling_price, production_date, expiry_date, stock_quantity)
VALUES 
('Smartphone XYZ', 15000.00, 18000.00, '2024-09-01', '2026-09-01', 200),
('Laptop ABC', 30000.00, 35000.00, '2024-08-15', '2026-08-15', 100),
('Bluetooth Headphones', 2000.00, 3000.00, '2024-07-10', '2025-07-10', 500),
('Organic Almonds', 500.00, 600.00, '2024-11-01', '2025-11-01', 150),
('Shampoo - Herbal', 150.00, 200.00, '2024-06-01', '2025-06-01', 300),
('Running Shoes - Sports', 2500.00, 3500.00, '2024-05-20', '2026-05-20', 50),
('Washing Powder - Eco-Friendly', 100.00, 150.00, '2024-04-10', '2025-04-10', 1000),
('Leather Wallet', 1200.00, 1800.00, '2024-03-01', '2026-03-01', 200),
('Cookware Set - Stainless Steel', 5000.00, 7000.00, '2024-02-15', '2026-02-15', 80),
('Baby Diapers - Premium', 800.00, 1200.00, '2024-10-05', '2025-10-05', 400);
