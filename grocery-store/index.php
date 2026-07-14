<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Home';

// Featured products (latest 8)
$stmt = $pdo->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8');
$featured = $stmt->fetchAll();

// All categories
$cats = $pdo->query('SELECT * FROM categories WHERE is_active = 1')->fetchAll();

$catIcons = ['🍅','🥛','🌾','🍵','🍪','🏠','💄','🥩'];
include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<div class="hero-banner mb-5">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h1 class="display-5 fw-bold">Fresh Groceries,<br>Delivered to Your Door</h1>
            <p class="lead mb-4">Shop fresh fruits, vegetables, dairy, and more — all from the comfort of your home.</p>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-warning btn-lg fw-bold">
                <i class="bi bi-basket me-2"></i>Shop Now
            </a>
        </div>
        <div class="col-md-5 text-center d-none d-md-block">
            <span style="font-size:120px;">🛒</span>
        </div>
    </div>
</div>

<!-- Categories -->
<section class="mb-5">
    <h3 class="fw-bold mb-3">Shop by Category</h3>
    <div class="row g-3">
        <?php foreach ($cats as $i => $cat): ?>
        <div class="col-6 col-md-3 col-lg-3">
            <a href="<?= BASE_URL ?>/products.php?cat=<?= $cat['id'] ?>" class="category-card card border-0 shadow-sm text-center p-3 d-block">
                <div class="category-icon bg-success bg-opacity-10"><?= $catIcons[$i % count($catIcons)] ?></div>
                <div class="fw-semibold small"><?= e($cat['name']) ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Featured Products</h3>
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline-success btn-sm">View All</a>
    </div>
    <div class="row g-3">
        <?php foreach ($featured as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 position-relative">
                <?php if ($p['stock_quantity'] <= 5): ?>
                    <span class="badge bg-danger badge-stock">Low Stock</span>
                <?php elseif ($p['stock_quantity'] == 0): ?>
                    <span class="badge bg-secondary badge-stock">Out of Stock</span>
                <?php endif; ?>
                <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
                    <img src="<?= UPLOAD_URL . e($p['image']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                <?php else: ?>
                    <div class="img-placeholder">🛍️</div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <small class="text-muted"><?= e($p['category_name']) ?></small>
                    <h6 class="card-title mt-1 mb-1"><?= e($p['name']) ?></h6>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success fs-5">Rs. <?= number_format($p['price'], 2) ?></span>
                        <span class="text-muted small">/<?= e($p['unit']) ?></span>
                    </div>
                    <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm mt-2">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Why Us -->
<section class="bg-white rounded-3 shadow-sm p-4 mb-5">
    <div class="row text-center g-4">
        <div class="col-md-3">
            <div class="fs-1">🚚</div>
            <h6 class="fw-bold">Fast Delivery</h6>
            <small class="text-muted">Same-day delivery available</small>
        </div>
        <div class="col-md-3">
            <div class="fs-1">🌿</div>
            <h6 class="fw-bold">Fresh Products</h6>
            <small class="text-muted">Sourced daily from local farms</small>
        </div>
        <div class="col-md-3">
            <div class="fs-1">💰</div>
            <h6 class="fw-bold">Best Prices</h6>
            <small class="text-muted">Competitive prices guaranteed</small>
        </div>
        <div class="col-md-3">
            <div class="fs-1">🔒</div>
            <h6 class="fw-bold">Secure Shopping</h6>
            <small class="text-muted">Your data is safe with us</small>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
