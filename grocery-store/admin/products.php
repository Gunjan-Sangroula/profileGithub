<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Manage Products';

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;
$total   = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$pages   = (int) ceil($total / $perPage);

$products = $pdo->query("
    SELECT p.*, c.name AS category_name FROM products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset
")->fetchAll();

$categories = $pdo->query('SELECT * FROM categories WHERE is_active = 1')->fetchAll();

include __DIR__ . '/includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Products</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </button>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php if ($p['image'] && file_exists(UPLOAD_DIR . $p['image'])): ?>
                            <img src="<?= UPLOAD_URL . e($p['image']) ?>" width="50" height="50" style="object-fit:cover;border-radius:6px;">
                        <?php else: ?>
                            <span style="font-size:32px;">🛍️</span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td>Rs. <?= number_format($p['price'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $p['stock_quantity'] == 0 ? 'danger' : ($p['stock_quantity'] <= 10 ? 'warning text-dark' : 'success') ?>">
                            <?= $p['stock_quantity'] ?>
                        </span>
                    </td>
                    <td><span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>"><?= $p['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <a href="?edit=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="<?= BASE_URL ?>/process/admin/product.php" method="post" class="d-inline" onsubmit="return confirm('Delete this product?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pages > 1): ?>
<nav><ul class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
    <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" <?= $editProduct ? 'data-bs-show="true"' : '' ?>>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/process/admin/product.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?= $editProduct ? 'edit' : 'add' ?>">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                        <input type="hidden" name="old_image" value="<?= e($editProduct['image'] ?? '') ?>">
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Product Name *</label>
                            <input type="text" name="name" class="form-control" required value="<?= $editProduct ? e($editProduct['name']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($editProduct && $editProduct['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price (Rs.) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?= $editProduct ? $editProduct['price'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" class="form-control" min="0" required value="<?= $editProduct ? $editProduct['stock_quantity'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg / litre / pack" value="<?= $editProduct ? e($editProduct['unit']) : 'kg' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= $editProduct ? e($editProduct['description']) : '' ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if ($editProduct && $editProduct['image'] && file_exists(UPLOAD_DIR . $editProduct['image'])): ?>
                                <img src="<?= UPLOAD_URL . e($editProduct['image']) ?>" height="60" class="mt-2 rounded">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= (!$editProduct || $editProduct['is_active']) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($editProduct && !$editProduct['is_active']) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><?= $editProduct ? 'Update Product' : 'Add Product' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($editProduct): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('productModal')).show();
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
