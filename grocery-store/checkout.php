<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Checkout';

$stmt = $pdo->prepare('
    SELECT c.quantity, p.id, p.name, p.price, p.unit, p.stock_quantity
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (empty($items)) {
    flash('error', 'Your cart is empty.');
    redirect(BASE_URL . '/cart.php');
}

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$delivery = $subtotal >= 500 ? 0 : 50;
$total    = $subtotal + $delivery;

$user = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();

include __DIR__ . '/includes/header.php';
?>

<h3 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Checkout</h3>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Shipping Information</h5>
                <form action="<?= BASE_URL ?>/process/checkout.php" method="post" id="checkoutForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= e($user['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" required value="<?= e($user['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3" required placeholder="Street, City, District, Province"><?= e($user['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="Cash on Delivery" id="cod" checked>
                            <label class="form-check-label" for="cod">
                                <i class="bi bi-cash-coin me-1 text-success"></i>Cash on Delivery
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="eSewa" id="esewa">
                            <label class="form-check-label" for="esewa">
                                <i class="bi bi-phone me-1 text-success"></i>eSewa
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Order Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any special delivery instructions..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold fs-5">
                        <i class="bi bi-check-circle me-2"></i>Place Order — Rs. <?= number_format($total, 2) ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Order Summary</h5>
                <?php foreach ($items as $item): ?>
                <div class="d-flex justify-content-between mb-2 small">
                    <span><?= e($item['name']) ?> × <?= $item['quantity'] ?></span>
                    <span>Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span>Rs. <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-success">
                    <span>Delivery</span>
                    <span><?= $delivery == 0 ? 'FREE' : 'Rs. ' . number_format($delivery, 2) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-success">Rs. <?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
