<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Customers';

$customers = $pdo->query('
    SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total_amount),0) AS total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id AND o.status != "cancelled"
    WHERE u.role = "customer"
    GROUP BY u.id
    ORDER BY u.created_at DESC
')->fetchAll();

include __DIR__ . '/includes/sidebar.php';
?>

<h4 class="fw-bold mb-4">Customers <span class="badge bg-secondary fs-6"><?= count($customers) ?></span></h4>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Joined</th></tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td class="fw-semibold"><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['phone'] ?? '—') ?></td>
                    <td><?= $u['order_count'] ?></td>
                    <td>Rs. <?= number_format($u['total_spent'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
