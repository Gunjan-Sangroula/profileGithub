<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'grocery_store');
define('BASE_URL', 'http://localhost:8000/grocery-store/');
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;margin:20px;">
        <strong>Database Connection Failed.</strong><br>
        Please ensure XAMPP MySQL is running and you have imported <code>database.sql</code>.<br>
        Error: ' . htmlspecialchars($e->getMessage()) . '
    </div>');
}

session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function flash(string $key, string $msg = null): ?string {
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return null;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function cartCount(): int {
    global $pdo;
    if (!isLoggedIn()) return 0;
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}

function wishlistCount(): int {
    global $pdo;
    if (!isLoggedIn()) return 0;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}
