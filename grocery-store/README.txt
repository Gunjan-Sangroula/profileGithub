====================================================
  FreshMart — Online Grocery Store
  BCA 4th Semester Academic Project
  Tribhuvan University | Mahendra Morang Campus
====================================================

HOW TO RUN
----------
1. Start XAMPP — make sure Apache and MySQL are running.

2. Copy this entire "grocery-store" folder into:
   C:\xampp\htdocs\grocery-store\

3. Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "New" to create a database (or let the SQL do it)
   - Click "Import" and select: database.sql
   - Click "Go"

4. Open your browser and visit:
  \
  
OR RUN BY USING PHP LOCAL SERVER
> php -S localhost:8000

====================================================
FEATURES
====================================================
Customer Side:
  - Register / Login / Logout
  - Browse products by category
  - Search products by keyword
  - View product details
  - Add to cart / wishlist
  - Checkout with Cash on Delivery or eSewa
  - View order history and tracking

Admin Panel (/admin/):
  - Dashboard with stats and low-stock alerts
  - Add / edit / delete products with image upload
  - Manage categories (add / edit)
  - View and update order status
  - View customer list

====================================================
DATABASE CREDENTIALS (config.php)
====================================================
  Host:     localhost
  User:     root
  Password: (empty by default in XAMPP)
  Database: grocery_store

  If your XAMPP MySQL has a password, edit config.php
  and change DB_PASS to your password.
====================================================
