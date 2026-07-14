<?php
require_once __DIR__ . '/config.php';
requireLogin();
if (isAdmin()) redirect(BASE_URL . '/admin/');
$pageTitle = 'My Cart';

$stmt = $pdo->prepare('
    SELECT c.id AS cart_id, c.quantity, p.id, p.name, p.price, p.unit, p.image, p.stock_quantity
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ? ORDER BY c.created_at DESC
');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));

include __DIR__ . '/includes/header.php';
?>

<h3 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>My Cart</h3>

<?php if (empty($items)): ?>
<div class="text-center py-5">
    <div class="fs-1">🛒</div>
    <h5 class="text-muted">Your cart is empty</h5>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-success mt-3">Start Shopping</a>
</div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <?php foreach ($items as $item): ?>
                <div class="d-flex align-items-center p-3 border-bottom">
                    <?php if ($item['image'] && file_exists(UPLOAD_DIR . $item['image'])): ?>
                        <img src="<?= UPLOAD_URL . e($item['image']) ?>" class="cart-item-img me-3" alt="">
                    <?php else: ?>
                        <div class="cart-item-img me-3 d-flex align-items-center justify-content-center bg-light rounded" style="font-size:32px;">🛍️</div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h6 class="mb-0"><?= e($item['name']) ?></h6>
                        <small class="text-muted">Rs. <?= number_format($item['price'], 2) ?> / <?= e($item['unit']) ?></small>
                    </div>
                    <form action="<?= BASE_URL ?>/process/cart.php" method="post" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <input type="hidden" name="action" value="update">
                        <div class="input-group" style="width:110px;">
                            <button type="button" class="btn btn-outline-secondary btn-sm qty-decrease">-</button>
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>" class="form-control form-control-sm text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm qty-increase">+</button>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Update"><i class="bi bi-arrow-clockwise"></i></button>
                    </form>
                    <div class="fw-bold text-success ms-3" style="min-width:90px;text-align:right;">
                        Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </div>
                    <form action="<?= BASE_URL ?>/process/cart.php" method="post" class="ms-2">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-2">
            <form action="<?= BASE_URL ?>/process/cart.php" method="post" class="d-inline">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Clear entire cart?')">
                    <i class="bi bi-trash me-1"></i>Clear Cart
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal (<?= count($items) ?> items)</span>
                    <span>Rs. <?= number_format($total, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Delivery</span>
                    <span><?= $total >= 500 ? 'FREE' : 'Rs. 50.00' ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-success">Rs. <?= number_format($total + ($total >= 500 ? 0 : 50), 2) ?></span>
                </div>
                <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-success w-100 mt-3 py-2 fw-bold">
                    <i class="bi bi-bag-check me-2"></i>Proceed to Checkout
                </a>
                <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline-secondary w-100 mt-2 btn-sm">Continue Shopping</a>
                <?php if ($total < 500): ?>
                <small class="text-muted d-block text-center mt-2">Add Rs. <?= number_format(500 - $total, 2) ?> more for free delivery!</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
