<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Security Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    return $slug . '-' . substr(md5(uniqid()), 0, 6);
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Story Functions
function getAllStories($limit = null, $offset = 0, $status = 'published', $category_id = null, $search = null) {
    $db = getDB();
    $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug, a.username as author_name
            FROM stories s
            LEFT JOIN categories c ON s.category_id = c.id
            LEFT JOIN admin_users a ON s.author_id = a.id
            WHERE s.status = :status";

    $params = ['status' => $status];

    if ($category_id) {
        $sql .= " AND s.category_id = :category_id";
        $params['category_id'] = $category_id;
    }

    if ($search) {
        $sql .= " AND (s.title LIKE :search OR s.content LIKE :search OR s.excerpt LIKE :search)";
        $params['search'] = "%$search%";
    }

    $sql .= " ORDER BY s.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }

    $stmt = $db->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    if ($limit) {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

function getFeaturedStories($limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("SELECT s.*, c.name as category_name, c.slug as category_slug
                          FROM stories s
                          LEFT JOIN categories c ON s.category_id = c.id
                          WHERE s.status = 'published' AND s.is_featured = 1
                          ORDER BY s.featured_order ASC, s.created_at DESC
                          LIMIT :limit");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getStoryBySlug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT s.*, c.name as category_name, c.slug as category_slug, a.username as author_name
                          FROM stories s
                          LEFT JOIN categories c ON s.category_id = c.id
                          LEFT JOIN admin_users a ON s.author_id = a.id
                          WHERE s.slug = :slug AND s.status = 'published'");
    $stmt->execute(['slug' => $slug]);
    return $stmt->fetch();
}

function getStoryById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM stories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function getTotalStories($status = 'published', $category_id = null, $search = null) {
    $db = getDB();
    $sql = "SELECT COUNT(*) FROM stories WHERE status = :status";
    $params = ['status' => $status];

    if ($category_id) {
        $sql .= " AND category_id = :category_id";
        $params['category_id'] = $category_id;
    }

    if ($search) {
        $sql .= " AND (title LIKE :search OR content LIKE :search OR excerpt LIKE :search)";
        $params['search'] = "%$search%";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function incrementViews($id) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE stories SET views = views + 1 WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Category Functions
function getAllCategories() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

function getCategoryById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// Image Upload Function
function uploadImage($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $fileSize = $file['size'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];

    if ($fileSize > MAX_FILE_SIZE) {
        return false;
    }

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
        return false;
    }

    $newFileName = uniqid('img_', true) . '.' . $fileExt;
    $uploadPath = UPLOAD_DIR . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return $newFileName;
    }

    return false;
}

function deleteImage($filename) {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
    }
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) {
        return 'just now';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

function truncate($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
