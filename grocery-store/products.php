<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Products';

$search  = trim($_GET['q'] ?? '');
$catId   = (int) ($_GET['cat'] ?? 0);
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

$where = ['p.is_active = 1'];
$params = [];

if ($search !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catId > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $catId;
}

$whereClause = implode(' AND ', $where);

$countSql = "SELECT COUNT(*) FROM products p WHERE $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = (int) ceil($total / $perPage);

$sql = "SELECT p.*, c.name AS category_name FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE $whereClause ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $pdo->query('SELECT * FROM categories WHERE is_active = 1')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="row g-3">
    <!-- Sidebar filters -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Categories</h6>
                <a href="<?= BASE_URL ?>/products.php" class="btn btn-sm <?= $catId == 0 ? 'btn-success' : 'btn-outline-secondary' ?> w-100 mb-1 text-start">
                    All Categories
                </a>
                <?php foreach ($cats as $cat): ?>
                <a href="<?= BASE_URL ?>/products.php?cat=<?= $cat['id'] ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                   class="btn btn-sm <?= $catId == $cat['id'] ? 'btn-success' : 'btn-outline-secondary' ?> w-100 mb-1 text-start">
                    <?= e($cat['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Products grid -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <?= $search ? 'Search: "' . e($search) . '"' : ($catId > 0 ? e($cats[array_search($catId, array_column($cats, 'id'))]['name'] ?? 'Products') : 'All Products') ?>
                <span class="text-muted fs-6 fw-normal ms-2">(<?= $total ?> items)</span>
            </h5>
        </div>

        <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <div class="fs-1">🔍</div>
            <h5 class="text-muted">No products found.</h5>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline-success mt-2">Clear Filters</a>
        </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($products as $p): ?>
            <div class="col-6 col-lg-4">
                <div class="card product-card h-100 position-relative">
                    <?php if ($p['stock_quantity'] == 0): ?>
                        <span class="badge bg-secondary badge-stock">Out of Stock</span>
                    <?php elseif ($p['stock_quantity'] <= 5): ?>
                        <span class="badge bg-danger badge-stock">Low Stock</span>
                    <?php endif; ?>
                    <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
                        <img src="<?= UPLOAD_URL . e($p['image']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                    <?php else: ?>
                        <div class="img-placeholder">🛍️</div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted"><?= e($p['category_name']) ?></small>
                        <h6 class="card-title mt-1"><?= e($p['name']) ?></h6>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">Rs. <?= number_format($p['price'], 2) ?></span>
                                <span class="text-muted small">/<?= e($p['unit']) ?></span>
                            </div>
                            <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
