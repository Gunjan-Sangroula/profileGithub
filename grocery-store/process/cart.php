<?php
require_once __DIR__ . '/../config.php';
requireLogin();
if (isAdmin()) { flash('error', 'Admins cannot use the cart.'); redirect(BASE_URL . '/admin/'); }

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty       = max(1, (int) ($_POST['quantity'] ?? 1));

        $p = $pdo->prepare('SELECT id, stock_quantity FROM products WHERE id = ? AND is_active = 1');
        $p->execute([$productId]);
        $product = $p->fetch();
        if (!$product) { flash('error', 'Product not found.'); break; }
        if ($product['stock_quantity'] < $qty) { flash('error', 'Not enough stock.'); break; }

        $stmt = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
        $stmt->execute([$_SESSION['user_id'], $productId, $qty]);
        flash('success', 'Added to cart!');
        break;

    case 'update':
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        $qty    = max(1, (int) ($_POST['quantity'] ?? 1));
        $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$qty, $cartId, $_SESSION['user_id']]);
        flash('success', 'Cart updated.');
        break;

    case 'remove':
        $cartId = (int) ($_POST['cart_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM cart WHERE id = ? AND user_id = ?');
        $stmt->execute([$cartId, $_SESSION['user_id']]);
        flash('success', 'Item removed from cart.');
        break;

    case 'clear':
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        flash('success', 'Cart cleared.');
        break;
}

$redirect = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/cart.php';
redirect($redirect);
