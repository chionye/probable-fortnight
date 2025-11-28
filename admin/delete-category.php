<?php
require_once '../includes/functions.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('categories.php');
}

$category = getCategoryById($id);
if (!$category) {
    redirect('categories.php');
}

$db = getDB();

// Delete category
$stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
$stmt->execute(['id' => $id]);

redirect('categories.php');
