<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-sidebar">
    <div class="brand"><i class="bi bi-basket2-fill me-2"></i>FreshMart Admin</div>
    <a href="<?= BASE_URL ?>/admin/" class="<?= $current == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i>Dashboard
    </a>
    <a href="<?= BASE_URL ?>/admin/products.php" class="<?= $current == 'products.php' ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i>Products
    </a>
    <a href="<?= BASE_URL ?>/admin/categories.php" class="<?= $current == 'categories.php' ? 'active' : '' ?>">
        <i class="bi bi-tags"></i>Categories
    </a>
    <a href="<?= BASE_URL ?>/admin/orders.php" class="<?= $current == 'orders.php' ? 'active' : '' ?>">
        <i class="bi bi-receipt"></i>Orders
    </a>
    <a href="<?= BASE_URL ?>/admin/customers.php" class="<?= $current == 'customers.php' ? 'active' : '' ?>">
        <i class="bi bi-people"></i>Customers
    </a>
    <hr style="border-color:#2a2a4a;margin:8px 0;">
    <a href="<?= BASE_URL ?>/" target="_blank"><i class="bi bi-house"></i>View Store</a>
    <a href="<?= BASE_URL ?>/logout.php" style="color:#f87171;"><i class="bi bi-box-arrow-right"></i>Logout</a>
</div>
<div class="admin-content">
<?php
$success = flash('success');
$error   = flash('error');
if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
