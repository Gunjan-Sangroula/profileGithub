<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/register.php');

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (!$name || !$email || !$password) {
    flash('error', 'Name, email, and password are required.');
    redirect(BASE_URL . '/register.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Please enter a valid email address.');
    redirect(BASE_URL . '/register.php');
}

if (strlen($password) < 6) {
    flash('error', 'Password must be at least 6 characters.');
    redirect(BASE_URL . '/register.php');
}

if ($password !== $confirm) {
    flash('error', 'Passwords do not match.');
    redirect(BASE_URL . '/register.php');
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$check->execute([$email]);
if ($check->fetch()) {
    flash('error', 'An account with this email already exists.');
    redirect(BASE_URL . '/register.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, "customer")');
$stmt->execute([$name, $email, $hash, $phone, $address]);

$userId = $pdo->lastInsertId();
$_SESSION['user_id']   = $userId;
$_SESSION['user_name'] = $name;
$_SESSION['role']      = 'customer';

flash('success', 'Account created successfully! Welcome to FreshMart.');
redirect(BASE_URL . '/');
