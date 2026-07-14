<?php
require_once __DIR__ . '/../config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/checkout.php');

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$payment = trim($_POST['payment_method'] ?? 'Cash on Delivery');
$notes   = trim($_POST['notes'] ?? '');

if (!$name || !$phone || !$address) {
    flash('error', 'Please fill in all required fields.');
    redirect(BASE_URL . '/checkout.php');
}

// Get cart items
$stmt = $pdo->prepare('SELECT c.quantity, p.id, p.name, p.price, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (empty($items)) {
    flash('error', 'Your cart is empty.');
    redirect(BASE_URL . '/cart.php');
}

// Validate stock
foreach ($items as $item) {
    if ($item['stock_quantity'] < $item['quantity']) {
        flash('error', "Sorry, '{$item['name']}' has insufficient stock.");
        redirect(BASE_URL . '/cart.php');
    }
}

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$delivery = $subtotal >= 500 ? 0 : 50;
$total    = $subtotal + $delivery;
$shippingAddress = "$name\n$phone\n$address";

try {
    $pdo->beginTransaction();

    // Create order
    $oStmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method, notes) VALUES (?, ?, "pending", ?, ?, ?)');
    $oStmt->execute([$_SESSION['user_id'], $total, $shippingAddress, $payment, $notes]);
    $orderId = $pdo->lastInsertId();

    // Insert order items and reduce stock
    $iStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
    $sStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');
    foreach ($items as $item) {
        $iStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        $sStmt->execute([$item['quantity'], $item['id']]);
    }

    // Clear cart
    $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$_SESSION['user_id']]);

    $pdo->commit();
    flash('success', "Order #$orderId placed successfully! We will contact you for delivery.");
    redirect(BASE_URL . '/orders.php?view=' . $orderId);
} catch (Exception $e) {
    $pdo->rollBack();
    flash('error', 'Order failed. Please try again.');
    redirect(BASE_URL . '/checkout.php');
}
