<?php
require_once __DIR__ . '/../../config.php';
requireAdmin();

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowed  = ['pending','processing','shipped','delivered','cancelled'];
$terminal = ['delivered', 'cancelled'];

if ($id && in_array($status, $allowed)) {
    // Check current status before updating
    $current = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
    $current->execute([$id]);
    $order = $current->fetch();

    if (!$order) {
        flash('error', 'Order not found.');
    } elseif (in_array($order['status'], $terminal)) {
        flash('error', 'This order is already ' . ucfirst($order['status']) . ' and cannot be changed.');
    } else {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash('success', "Order #$id status updated to " . ucfirst($status) . '.');
    }
} else {
    flash('error', 'Invalid status.');
}

redirect(BASE_URL . '/admin/orders.php?view=' . $id);
