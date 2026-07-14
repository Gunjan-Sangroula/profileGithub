<?php
require_once __DIR__ . '/../../config.php';
requireAdmin();

$action = $_POST['action'] ?? '';

if ($action === 'add' || $action === 'edit') {
    $name        = trim($_POST['name'] ?? '');
    $categoryId  = (int) ($_POST['category_id'] ?? 0);
    $price       = (float) ($_POST['price'] ?? 0);
    $stock       = (int) ($_POST['stock_quantity'] ?? 0);
    $unit        = trim($_POST['unit'] ?? 'kg');
    $description = trim($_POST['description'] ?? '');
    $isActive    = (int) ($_POST['is_active'] ?? 1);

    if (!$name || !$categoryId || $price <= 0) {
        flash('error', 'Name, category, and price are required.');
        redirect(BASE_URL . '/admin/products.php');
    }

    $imageName = $_POST['old_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            flash('error', 'Only image files are allowed (jpg, png, gif, webp).');
            redirect(BASE_URL . '/admin/products.php');
        }
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            flash('error', 'Image size must be under 5MB.');
            redirect(BASE_URL . '/admin/products.php');
        }
        $imageName = uniqid('prod_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $imageName);
    }

    if ($action === 'add') {
        $stmt = $pdo->prepare('INSERT INTO products (category_id, name, description, price, stock_quantity, unit, image, is_active) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$categoryId, $name, $description, $price, $stock, $unit, $imageName, $isActive]);
        flash('success', 'Product added successfully.');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=?, unit=?, image=?, is_active=? WHERE id=?');
        $stmt->execute([$categoryId, $name, $description, $price, $stock, $unit, $imageName, $isActive, $id]);
        flash('success', 'Product updated successfully.');
    }
} elseif ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE products SET is_active = 0 WHERE id = ?')->execute([$id]);
    flash('success', 'Product deleted.');
}

redirect(BASE_URL . '/admin/products.php');
