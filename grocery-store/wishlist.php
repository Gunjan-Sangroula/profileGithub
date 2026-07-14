<?php
require_once __DIR__ . '/config.php';
requireLogin();
if (isAdmin()) redirect(BASE_URL . '/admin/');
$pageTitle = 'My Wishlist';

$stmt = $pdo->prepare('
    SELECT w.id AS wish_id, p.id, p.name, p.price, p.unit, p.image, p.stock_quantity
    FROM wishlist w JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ? ORDER BY w.created_at DESC
');
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<h3 class="fw-bold mb-4"><i class="bi bi-heart me-2"></i>My Wishlist</h3>

<?php if (empty($items)): ?>
<div class="text-center py-5">
    <div class="fs-1">💛</div>
    <h5 class="text-muted">Your wishlist is empty</h5>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-success mt-3">Browse Products</a>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($items as $item): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card product-card h-100">
            <?php if ($item['image'] && file_exists(UPLOAD_DIR . $item['image'])): ?>
                <img src="<?= UPLOAD_URL . e($item['image']) ?>" class="card-img-top" alt="<?= e($item['name']) ?>">
            <?php else: ?>
                <div class="img-placeholder" style="height:160px;font-size:48px;">🛍️</div>
            <?php endif; ?>
            <div class="card-body">
                <h6 class="card-title"><?= e($item['name']) ?></h6>
                <div class="fw-bold text-success mb-2">Rs. <?= number_format($item['price'], 2) ?> <small class="text-muted fw-normal">/<?= e($item['unit']) ?></small></div>
                <?php if ($item['stock_quantity'] > 0): ?>
                <form action="<?= BASE_URL ?>/process/cart.php" method="post" class="mb-2">
                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-cart-plus me-1"></i>Add to Cart
                    </button>
                </form>
                <?php else: ?>
                    <span class="badge bg-secondary w-100 mb-2">Out of Stock</span>
                <?php endif; ?>
                <form action="<?= BASE_URL ?>/process/wishlist.php" method="post">
                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="action" value="remove">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-heart-fill me-1"></i>Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
