<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>FreshMart Grocery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= BASE_URL ?>/">
            <i class="bi bi-basket2-fill me-2"></i>FreshMart
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/products.php">Products</a></li>
            </ul>
            <form class="d-flex me-3" action="<?= BASE_URL ?>/products.php" method="get">
                <input class="form-control form-control-sm me-2" type="search" name="q"
                    placeholder="Search products..." value="<?= isset($_GET['q']) ? e($_GET['q']) : '' ?>">
                <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <?php if (!isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/wishlist.php">
                            <i class="bi bi-heart"></i>
                            <?php $wc = wishlistCount(); if ($wc > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?= $wc ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/cart.php">
                            <i class="bi bi-cart3"></i>
                            <?php $cc = cartCount(); if ($cc > 0): ?>
                                <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle"><?= $cc ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= e($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (!isAdmin()): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders.php"><i class="bi bi-box me-2"></i>My Orders</a></li>
                            <?php endif; ?>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-2 my-auto" href="<?= BASE_URL ?>/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
<div class="container">
<?php
$success = flash('success');
$error   = flash('error');
if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
