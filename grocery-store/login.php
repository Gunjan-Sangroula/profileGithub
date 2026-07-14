<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) redirect(BASE_URL . '/');
$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="fs-1">🛒</div>
                    <h3 class="fw-bold">Welcome Back</h3>
                    <p class="text-muted small">Login to your FreshMart account</p>
                </div>
                <form action="<?= BASE_URL ?>/process/login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
                <hr>
                <p class="text-center text-muted small mb-0">
                    Don't have an account? <a href="<?= BASE_URL ?>/register.php" class="text-success fw-semibold">Register here</a>
                </p>
                <p class="text-center text-muted small mt-2">
                    <strong>Demo:</strong> admin@grocery.com / admin123
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
