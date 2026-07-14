<?php
require_once __DIR__ . '/config.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) { redirect(BASE_URL . '/products.php'); }

$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { flash('error', 'Product not found.'); redirect(BASE_URL . '/products.php'); }

$pageTitle = $p['name'];

// Check wishlist
$inWishlist = false;
if (isLoggedIn()) {
    $ws = $pdo->prepare('SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?');
    $ws->execute([$_SESSION['user_id'], $id]);
    $inWishlist = (bool) $ws->fetch();
}

// Related products
$rel = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 LIMIT 4');
$rel->execute([$p['category_id'], $id]);
$related = $rel->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products.php?cat=<?= $p['category_id'] ?>"><?= e($p['category_name']) ?></a></li>
        <li class="breadcrumb-item active"><?= e($p['name']) ?></li>
    </ol>
</nav>

<div class="row g-4 mb-5">
    <div class="col-md-5">
        <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
            <img src="<?= UPLOAD_URL . e($p['image']) ?>" class="img-fluid rounded-3 shadow w-100" style="max-height:380px;object-fit:cover;" alt="<?= e($p['name']) ?>">
        <?php else: ?>
            <div class="img-placeholder rounded-3 shadow" style="height:380px;font-size:96px;">🛍️</div>
        <?php endif; ?>
    </div>
    <div class="col-md-7">
        <small class="text-muted"><?= e($p['category_name']) ?></small>
        <h2 class="fw-bold mt-1 mb-2"><?= e($p['name']) ?></h2>
        <div class="fs-3 fw-bold text-success mb-1">Rs. <?= number_format($p['price'], 2) ?> <small class="fs-6 text-muted">/ <?= e($p['unit']) ?></small></div>

        <?php if ($p['stock_quantity'] > 0): ?>
            <span class="badge bg-success mb-3"><i class="bi bi-check-circle me-1"></i>In Stock (<?= $p['stock_quantity'] ?> available)</span>
        <?php else: ?>
            <span class="badge bg-danger mb-3"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>
        <?php endif; ?>

        <p class="text-muted"><?= nl2br(e($p['description'])) ?></p>

        <?php if (!isAdmin()): ?>
        <?php if ($p['stock_quantity'] > 0): ?>
        <form action="<?= BASE_URL ?>/process/cart.php" method="post" class="mb-3">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="action" value="add">
            <div class="d-flex align-items-center gap-3">
                <div class="input-group" style="width:130px;">
                    <button type="button" class="btn btn-outline-secondary qty-decrease">-</button>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $p['stock_quantity'] ?>" class="form-control text-center">
                    <button type="button" class="btn btn-outline-secondary qty-increase">+</button>
                </div>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                </button>
            </div>
        </form>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/process/wishlist.php" method="post">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="action" value="<?= $inWishlist ? 'remove' : 'add' ?>">
            <button type="submit" class="btn btn-outline-<?= $inWishlist ? 'danger' : 'secondary' ?> btn-sm">
                <i class="bi bi-heart<?= $inWishlist ? '-fill' : '' ?> me-1"></i>
                <?= $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' ?>
            </button>
        </form>
        <?php else: ?>
        <div class="alert alert-info py-2 px-3 d-inline-flex align-items-center gap-2 mt-2">
            <i class="bi bi-shield-lock"></i>
            <span>You are logged in as admin. <a href="<?= BASE_URL ?>/admin/products.php" class="alert-link">Manage this product</a> in the Admin Panel.</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Related products -->
<?php if (!empty($related)): ?>
<section>
    <h5 class="fw-bold mb-3">Related Products</h5>
    <div class="row g-3">
        <?php foreach ($related as $r): ?>
        <div class="col-6 col-md-3">
            <div class="card product-card h-100">
                <?php if ($r['image'] && file_exists(UPLOAD_DIR . $r['image'])): ?>
                    <img src="<?= UPLOAD_URL . e($r['image']) ?>" class="card-img-top" alt="<?= e($r['name']) ?>">
                <?php else: ?>
                    <div class="img-placeholder" style="height:140px;font-size:40px;">🛍️</div>
                <?php endif; ?>
                <div class="card-body">
                    <h6 class="card-title small"><?= e($r['name']) ?></h6>
                    <div class="fw-bold text-success small">Rs. <?= number_format($r['price'], 2) ?></div>
                    <a href="<?= BASE_URL ?>/product.php?id=<?= $r['id'] ?>" class="btn btn-outline-success btn-sm w-100 mt-2">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
