<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/login.php');

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    flash('error', 'Please fill in all fields.');
    redirect(BASE_URL . '/login.php');
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    flash('error', 'Invalid email or password.');
    redirect(BASE_URL . '/login.php');
}

$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['role']      = $user['role'];

$redirect = $_SESSION['redirect_after_login'] ?? null;
unset($_SESSION['redirect_after_login']);

flash('success', 'Welcome back, ' . $user['name'] . '!');
redirect($redirect ?? BASE_URL . '/');
