<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Manage Orders';

$viewId = (int) ($_GET['view'] ?? 0);
$viewOrder = null;
$orderItems = [];

if ($viewId) {
    $os = $pdo->prepare('SELECT o.*, u.name AS customer_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?');
    $os->execute([$viewId]);
    $viewOrder = $os->fetch();
    if ($viewOrder) {
        $ois = $pdo->prepare('SELECT oi.*, p.name, p.unit FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
        $ois->execute([$viewId]);
        $orderItems = $ois->fetchAll();
    }
}

$filterStatus = $_GET['status'] ?? '';
$where = $filterStatus ? 'WHERE o.status = ?' : '';
$params = $filterStatus ? [$filterStatus] : [];
$stmt = $pdo->prepare("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['pending','processing','shipped','delivered','cancelled'];
$terminalStatuses = ['delivered', 'cancelled'];

include __DIR__ . '/includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Orders</h4>
    <div class="d-flex gap-2 flex-wrap">
        <a href="?" class="btn btn-sm <?= !$filterStatus ? 'btn-success' : 'btn-outline-secondary' ?>">All</a>
        <?php foreach ($statuses as $s): ?>
        <a href="?status=<?= $s ?>" class="btn btn-sm <?= $filterStatus == $s ? 'btn-success' : 'btn-outline-secondary' ?>"><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($viewOrder): ?>
<a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left me-1"></i>Back</a>
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">Order #<?= $viewOrder['id'] ?></h5>
                <p class="text-muted small">Placed: <?= date('d M Y, h:i A', strtotime($viewOrder['created_at'])) ?></p>
                <p><strong>Customer:</strong> <?= e($viewOrder['customer_name']) ?><br>
                   <strong>Email:</strong> <?= e($viewOrder['email']) ?><br>
                   <strong>Phone:</strong> <?= e($viewOrder['phone'] ?? '—') ?></p>
                <p><strong>Shipping:</strong><br><span class="text-muted"><?= nl2br(e($viewOrder['shipping_address'])) ?></span></p>
                <p><strong>Payment:</strong> <?= e($viewOrder['payment_method']) ?></p>
                <?php if ($viewOrder['notes']): ?>
                <p><strong>Notes:</strong> <?= e($viewOrder['notes']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <?php if (in_array($viewOrder['status'], $terminalStatuses)): ?>
                    <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-lock-fill fs-5"></i>
                        <span>This order is <strong><?= ucfirst($viewOrder['status']) ?></strong> and cannot be modified.</span>
                    </div>
                <?php else: ?>
                <form action="<?= BASE_URL ?>/process/admin/order-status.php" method="post" class="mb-3">
                    <input type="hidden" name="id" value="<?= $viewOrder['id'] ?>">
                    <label class="form-label fw-semibold">Update Status</label>
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select">
                            <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $viewOrder['status'] == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-bordered mt-2">
            <thead class="table-light"><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
            <tbody>
                <?php foreach ($orderItems as $oi): ?>
                <tr>
                    <td><?= e($oi['name']) ?></td>
                    <td><?= $oi['quantity'] ?> <?= e($oi['unit']) ?></td>
                    <td>Rs. <?= number_format($oi['unit_price'], 2) ?></td>
                    <td>Rs. <?= number_format($oi['unit_price'] * $oi['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end fw-bold">Grand Total</td><td class="fw-bold text-success">Rs. <?= number_format($viewOrder['total_amount'], 2) ?></td></tr>
            </tfoot>
        </table>
    </div>
</div>
<?php else: ?>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr><th>#</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="fw-bold">#<?= $o['id'] ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td>Rs. <?= number_format($o['total_amount'], 2) ?></td>
                    <td><?= e($o['payment_method']) ?></td>
                    <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><a href="?view=<?= $o['id'] ?>" class="btn btn-outline-primary btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
