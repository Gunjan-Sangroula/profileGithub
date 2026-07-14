<?php
require_once __DIR__ . '/../../config.php';
requireAdmin();

$action = $_POST['action'] ?? '';
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$parentId    = ($_POST['parent_id'] ?? '') !== '' ? (int) $_POST['parent_id'] : null;
$isActive    = (int) ($_POST['is_active'] ?? 1);

if ($action === 'add') {
    if (!$name) { flash('error', 'Name is required.'); redirect(BASE_URL . '/admin/categories.php'); }
    $stmt = $pdo->prepare('INSERT INTO categories (name, description, parent_id, is_active) VALUES (?,?,?,?)');
    $stmt->execute([$name, $description, $parentId, $isActive]);
    flash('success', 'Category added.');
} elseif ($action === 'edit') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('UPDATE categories SET name=?, description=?, parent_id=?, is_active=? WHERE id=?');
    $stmt->execute([$name, $description, $parentId, $isActive, $id]);
    flash('success', 'Category updated.');
} elseif ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE categories SET is_active = 0 WHERE id = ?')->execute([$id]);
    flash('success', 'Category deactivated.');
}

redirect(BASE_URL . '/admin/categories.php');
