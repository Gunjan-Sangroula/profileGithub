<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) redirect(BASE_URL . '/');
$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="fs-1">🌿</div>
                    <h3 class="fw-bold">Create Account</h3>
                    <p class="text-muted small">Join FreshMart today</p>
                </div>
                <form action="<?= BASE_URL ?>/process/register.php" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Your full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="98XXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street, City, District"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="At least 6 characters">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat password">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </form>
                <hr>
                <p class="text-center text-muted small mb-0">
                    Already have an account? <a href="<?= BASE_URL ?>/login.php" class="text-success fw-semibold">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
