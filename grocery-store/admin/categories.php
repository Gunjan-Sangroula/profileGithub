<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Manage Categories';

$editCat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editCat = $stmt->fetch();
}

$categories = $pdo->query('SELECT c.*, p.name AS parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id ORDER BY c.id')->fetchAll();

include __DIR__ . '/includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Categories</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#catModal">
        <i class="bi bi-plus-lg me-1"></i>Add Category
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr><th>ID</th><th>Name</th><th>Parent</th><th>Description</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td class="fw-semibold"><?= e($cat['name']) ?></td>
                    <td><?= $cat['parent_name'] ? e($cat['parent_name']) : '<span class="text-muted">—</span>' ?></td>
                    <td><small class="text-muted"><?= e(mb_strimwidth($cat['description'] ?? '', 0, 60, '...')) ?></small></td>
                    <td><span class="badge bg-<?= $cat['is_active'] ? 'success' : 'secondary' ?>"><?= $cat['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <a href="?edit=<?= $cat['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="<?= BASE_URL ?>/process/admin/category.php" method="post" class="d-inline" onsubmit="return confirm('Delete this category?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><?= $editCat ? 'Edit Category' : 'Add Category' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/process/admin/category.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?= $editCat ? 'edit' : 'add' ?>">
                    <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= $editCat ? e($editCat['name']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (top-level)</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php if (!$editCat || $cat['id'] != $editCat['id']): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($editCat && $editCat['parent_id'] == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?= $editCat ? e($editCat['description']) : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?= (!$editCat || $editCat['is_active']) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ($editCat && !$editCat['is_active']) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><?= $editCat ? 'Update' : 'Add Category' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($editCat): ?>
<script>document.addEventListener('DOMContentLoaded', () => { new bootstrap.Modal(document.getElementById('catModal')).show(); });</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
