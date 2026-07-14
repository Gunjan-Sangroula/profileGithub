<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Dashboard';

$totalProducts  = $pdo->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn();
$totalOrders    = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalCustomers = $pdo->query('SELECT COUNT(*) FROM users WHERE role = "customer"')->fetchColumn();
$totalRevenue   = $pdo->query('SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != "cancelled"')->fetchColumn();

$recentOrders = $pdo->query('
    SELECT o.*, u.name AS customer_name FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 10
')->fetchAll();

$lowStock = $pdo->query('SELECT * FROM products WHERE stock_quantity <= 10 AND is_active = 1 ORDER BY stock_quantity ASC LIMIT 5')->fetchAll();

include __DIR__ . '/includes/sidebar.php';
?>

<h4 class="fw-bold mb-4">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#20c997)">
            <div class="fs-1 fw-bold"><?= $totalProducts ?></div>
            <div><i class="bi bi-box-seam me-1"></i>Total Products</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0dcaf0)">
            <div class="fs-1 fw-bold"><?= $totalOrders ?></div>
            <div><i class="bi bi-receipt me-1"></i>Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6610f2,#d63384)">
            <div class="fs-1 fw-bold"><?= $totalCustomers ?></div>
            <div><i class="bi bi-people me-1"></i>Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#fd7e14,#ffc107)">
            <div class="fs-1 fw-bold">Rs.<?= number_format($totalRevenue) ?></div>
            <div><i class="bi bi-currency-exchange me-1"></i>Revenue</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">Recent Orders</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><a href="<?= BASE_URL ?>/admin/orders.php?view=<?= $o['id'] ?>">#<?= $o['id'] ?></a></td>
                            <td><?= e($o['customer_name']) ?></td>
                            <td>Rs. <?= number_format($o['total_amount'], 2) ?></td>
                            <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alert</div>
            <ul class="list-group list-group-flush">
                <?php if (empty($lowStock)): ?>
                    <li class="list-group-item text-muted">All products have sufficient stock.</li>
                <?php else: ?>
                    <?php foreach ($lowStock as $p): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($p['name']) ?></span>
                        <span class="badge bg-<?= $p['stock_quantity'] == 0 ? 'danger' : 'warning text-dark' ?>"><?= $p['stock_quantity'] ?> left</span>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
