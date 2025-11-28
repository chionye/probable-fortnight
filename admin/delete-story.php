<?php
require_once '../includes/functions.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('stories.php');
}

$story = getStoryById($id);
if (!$story) {
    redirect('stories.php');
}

$db = getDB();

// Delete image if exists
if ($story['image']) {
    deleteImage($story['image']);
}

// Delete story
$stmt = $db->prepare("DELETE FROM stories WHERE id = :id");
$stmt->execute(['id' => $id]);

redirect('stories.php');
