# FreshMart — Setup Guide

## Prerequisites

Make sure the following are already installed and running on your system:

- **Apache** web server
- **MySQL** (or MariaDB)
- **PHP 7.4+** (PHP 8.x recommended)
- **phpMyAdmin**

---

## Step 1 — Copy the Project Files

Place the `grocery-store` folder inside your Apache web root directory.

**Common web root locations:**

| OS      | Path                          |
|---------|-------------------------------|
| Windows | `C:\Apache24\htdocs\`         |
| Ubuntu  | `/var/www/html/`              |
| macOS   | `/Library/WebServer/Documents/` |

Your folder should end up at:
```
<web-root>/grocery-store/where.exe php
php -v
```

---

## Step 2 — Create the Database

1. Open phpMyAdmin in your browser (usually at `http://localhost/phpmyadmin`)
2. In the left sidebar, click **New**
3. Enter database name: `grocery_store`
4. Set collation to: `utf8_general_ci`
5. Click **Create**

---

## Step 3 — Import the Database

1. In phpMyAdmin, click on `grocery_store` in the left sidebar
2. Click the **Import** tab at the top
3. Click **Choose File** and select `database.sql` from the project folder
4. Click **Go**

You should see: *"Import has been successfully finished"*

This creates all 7 tables and inserts sample categories, products, and an admin account.

---

## Step 4 — Configure Database Connection

Open `config.php` in the project root and update it to match your server credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', '');            // your MySQL password
define('DB_NAME', 'grocery_store');
define('BASE_URL', 'http://localhost/grocery-store');
```

> If your Apache is running on a different port (e.g. 8080), update `BASE_URL` accordingly:
> ```php
> define('BASE_URL', 'http://localhost:8080/grocery-store');
> ```

---

## Step 5 — Set Upload Folder Permissions

The `uploads/products/` folder must be writable by Apache for product image uploads to work.

**Linux / macOS:**
```bash
chmod -R 775 /var/www/html/grocery-store/uploads/
chown -R www-data:www-data /var/www/html/grocery-store/uploads/
```

**Windows:**
Right-click `uploads/` → Properties → Security → give the Apache user (or `Everyone`) **Write** permission.

---

## Step 6 — Open the Application

Start Apache and MySQL if not already running, then open your browser:

```
http://localhost/grocery-store/
```

The FreshMart home page should load with sample products.

---

## Login Credentials

### Admin Account
| Field    | Value                                        |
|----------|----------------------------------------------|
| URL      | `http://localhost/grocery-store/admin/`      |
| Email    | `admin@grocery.com`                          |
| Password | `admin123`                                   |

### Sample Customer Account
| Field    | Value               |
|----------|---------------------|
| Email    | `ramesh@example.com`|
| Password | `customer123`       |

You can also register a new customer at:
```
http://localhost/grocery-store/register.php
```

---

## Project Structure

```
grocery-store/
├── config.php              ← Database connection + helper functions
├── database.sql            ← Database schema + sample data
├── index.php               ← Home page
├── products.php            ← Browse & search products
├── product.php             ← Product detail page
├── cart.php                ← Shopping cart
├── wishlist.php            ← Wishlist
├── checkout.php            ← Place an order
├── orders.php              ← Order history & tracking
├── login.php
├── register.php
├── logout.php
│
├── admin/                  ← Admin panel
│   ├── index.php           ← Dashboard
│   ├── products.php        ← Manage products
│   ├── categories.php      ← Manage categories
│   ├── orders.php          ← View & update orders
│   └── customers.php       ← View customers
│
├── process/                ← Form POST handlers
│   ├── login.php
│   ├── register.php
│   ├── cart.php
│   ├── wishlist.php
│   ├── checkout.php
│   └── admin/
│       ├── product.php
│       ├── category.php
│       └── order-status.php
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── css/style.css
│   └── js/main.js
│
└── uploads/products/       ← Uploaded product images
```

---

## Tech Stack

| Layer      | Technology                  |
|------------|-----------------------------|
| Frontend   | HTML5, CSS3, JavaScript     |
| UI Library | Bootstrap 5 (CDN)           |
| Backend    | PHP 8                       |
| Database   | MySQL / MariaDB             |
| Server     | Apache                      |
| Admin DB   | phpMyAdmin                  |

---

## Common Issues

### Blank page or 500 error
- Enable PHP error display temporarily in `php.ini`:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
  Then restart Apache.

### "Access denied for user 'root'@'localhost'"
- Wrong MySQL credentials. Update `DB_USER` and `DB_PASS` in `config.php`.

### Images not uploading
- Check `uploads/products/` exists and Apache has write permission (see Step 5).
- Verify `file_uploads = On` in your `php.ini`.

### CSS / JS not loading
- Make sure `BASE_URL` in `config.php` exactly matches the URL you use in the browser (including port number if non-standard).

---

## Academic Project Info

| Field       | Details                                      |
|-------------|----------------------------------------------|
| Project     | Online Grocery Store                         |
| Course      | BCA 4th Semester                             |
| University  | Tribhuvan University                         |
| Campus      | Mahendra Morang Adarsh Multiple Campus       |
| Location    | Biratnagar, Morang, Nepal                    |
| Students    | Gunjan Sangroula, Arpan Kathet               |
