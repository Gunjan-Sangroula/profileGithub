<?php
require_once __DIR__ . '/../config.php';
requireLogin();
if (isAdmin()) { flash('error', 'Admins cannot use the wishlist.'); redirect(BASE_URL . '/admin/'); }

$productId = (int) ($_POST['product_id'] ?? 0);
$action    = $_POST['action'] ?? 'add';

if ($action === 'add') {
    $stmt = $pdo->prepare('INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $productId]);
    flash('success', 'Added to wishlist!');
} else {
    $stmt = $pdo->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$_SESSION['user_id'], $productId]);
    flash('success', 'Removed from wishlist.');
}

$redirect = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/wishlist.php';
redirect($redirect);
