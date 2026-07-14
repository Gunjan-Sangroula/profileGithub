<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'My Orders';

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$viewId = (int) ($_GET['view'] ?? 0);
$viewOrder = null;
$orderItems = [];
if ($viewId) {
    $os = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
    $os->execute([$viewId, $_SESSION['user_id']]);
    $viewOrder = $os->fetch();
    if ($viewOrder) {
        $ois = $pdo->prepare('SELECT oi.*, p.name, p.image, p.unit FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
        $ois->execute([$viewId]);
        $orderItems = $ois->fetchAll();
    }
}

$statusColors = [
    'pending'    => 'warning',
    'processing' => 'info',
    'shipped'    => 'primary',
    'delivered'  => 'success',
    'cancelled'  => 'danger',
];

include __DIR__ . '/includes/header.php';
?>

<h3 class="fw-bold mb-4"><i class="bi bi-box me-2"></i>My Orders</h3>

<?php if ($viewOrder): ?>
<!-- Order detail view -->
<a href="<?= BASE_URL ?>/orders.php" class="btn btn-outline-secondary btn-sm mb-3">
    <i class="bi bi-arrow-left me-1"></i>Back to Orders
</a>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5 class="fw-bold">Order #<?= $viewOrder['id'] ?></h5>
                <small class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($viewOrder['created_at'])) ?></small>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge badge-<?= $viewOrder['status'] ?> px-3 py-2 fs-6">
                    <?= ucfirst($viewOrder['status']) ?>
                </span>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <strong>Shipping Address:</strong><br>
                <span class="text-muted"><?= nl2br(e($viewOrder['shipping_address'])) ?></span>
            </div>
            <div class="col-md-6">
                <strong>Payment:</strong> <?= e($viewOrder['payment_method']) ?><br>
                <?php if ($viewOrder['notes']): ?>
                <strong>Notes:</strong> <?= e($viewOrder['notes']) ?>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $oi): ?>
                <tr>
                    <td><?= e($oi['name']) ?></td>
                    <td><?= $oi['quantity'] ?></td>
                    <td>Rs. <?= number_format($oi['unit_price'], 2) ?></td>
                    <td>Rs. <?= number_format($oi['unit_price'] * $oi['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="fw-bold text-success">Rs. <?= number_format($viewOrder['total_amount'], 2) ?></td></tr>
            </tfoot>
        </table>
    </div>
</div>

<?php elseif (empty($orders)): ?>
<div class="text-center py-5">
    <div class="fs-1">📦</div>
    <h5 class="text-muted">No orders yet</h5>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-success mt-3">Start Shopping</a>
</div>
<?php else: ?>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="fw-bold">#<?= $order['id'] ?></td>
                    <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                    <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= e($order['payment_method']) ?></td>
                    <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                    <td><a href="?view=<?= $order['id'] ?>" class="btn btn-outline-success btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
