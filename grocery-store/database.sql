-- Online Grocery Store Database
-- Import this file via phpMyAdmin

CREATE DATABASE IF NOT EXISTS grocery_store;
USE grocery_store;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    unit VARCHAR(20) DEFAULT 'kg',
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- Wishlist table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (user_id, product_id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, phone, role) VALUES
('Admin', 'admin@grocery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9800000000', 'admin');

-- Sample customer (password: customer123)
INSERT INTO users (name, email, password, phone, address) VALUES
('Ramesh Thapa', 'ramesh@example.com', '$2y$10$TKh8H1.PtIqpL7/VDP4u.euOZGFLInKx6oqtxS7jhiQ4CmYPOYMti', '9812345678', 'Biratnagar, Morang');

-- Categories
INSERT INTO categories (name, description) VALUES
('Fruits & Vegetables', 'Fresh fruits and vegetables from local farms'),
('Dairy & Eggs', 'Milk, cheese, butter, and fresh eggs'),
('Grains & Pulses', 'Rice, wheat, lentils, and other grains'),
('Beverages', 'Tea, coffee, juice, and cold drinks'),
('Snacks & Bakery', 'Biscuits, chips, bread, and bakery items'),
('Household Essentials', 'Cleaning products and household items'),
('Personal Care', 'Soap, shampoo, and personal care products'),
('Meat & Fish', 'Fresh meat, chicken, and fish');

-- Products
INSERT INTO products (category_id, name, description, price, stock_quantity, unit) VALUES
(1, 'Fresh Tomatoes', 'Locally grown, fresh red tomatoes. Perfect for cooking and salads.', 60.00, 100, 'kg'),
(1, 'Onions', 'Fresh farm onions, rich in flavor and essential for every kitchen.', 40.00, 150, 'kg'),
(1, 'Potatoes', 'Premium quality potatoes, great for curries and fries.', 35.00, 200, 'kg'),
(1, 'Carrots', 'Crunchy and nutritious fresh carrots.', 50.00, 80, 'kg'),
(1, 'Bananas', 'Fresh ripe bananas, rich in potassium and energy.', 80.00, 60, 'dozen'),
(1, 'Apples', 'Juicy and sweet apples, imported premium quality.', 200.00, 50, 'kg'),
(2, 'Full Cream Milk', 'Fresh full cream pasteurized milk from local dairy.', 90.00, 40, 'litre'),
(2, 'Eggs (12 pcs)', 'Farm fresh eggs, pack of 12.', 180.00, 70, 'pack'),
(2, 'Butter', 'Pure creamy butter, perfect for cooking and baking.', 120.00, 30, 'pack'),
(2, 'Paneer', 'Fresh homemade style paneer, soft and creamy.', 200.00, 25, 'kg'),
(3, 'Basmati Rice', 'Premium long grain basmati rice, aromatic and fluffy.', 120.00, 100, 'kg'),
(3, 'Whole Wheat Flour', 'Finely ground whole wheat flour (Atta) for chapati.', 65.00, 80, 'kg'),
(3, 'Red Lentils', 'High quality red lentils (Masoor Dal), rich in protein.', 110.00, 60, 'kg'),
(3, 'Chickpeas', 'Dried chickpeas (Chana), high in fiber and protein.', 130.00, 50, 'kg'),
(4, 'Green Tea', 'Premium green tea bags, pack of 25. Refreshing and healthy.', 150.00, 45, 'pack'),
(4, 'Orange Juice', 'Fresh squeezed orange juice, no added sugar, 1 litre.', 120.00, 35, 'litre'),
(4, 'Black Coffee', 'Rich and bold instant black coffee powder, 200g.', 250.00, 40, 'pack'),
(5, 'Digestive Biscuits', 'Wholesome digestive biscuits, great with tea or coffee.', 80.00, 60, 'pack'),
(5, 'Potato Chips', 'Crunchy salted potato chips, 100g pack.', 50.00, 90, 'pack'),
(6, 'Dish Wash Liquid', 'Effective dish washing liquid, lemon fresh, 500ml.', 110.00, 55, 'bottle');
