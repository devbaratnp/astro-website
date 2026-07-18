<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) jsonError('Unauthenticated', 401);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);

$type = $_POST['type'] ?? 'general';
$allowedDirs = [
    'article' => __DIR__ . '/../uploads/articles/',
    'gallery' => __DIR__ . '/../uploads/gallery/',
    'general' => __DIR__ . '/../uploads/general/',
];

$dir = $allowedDirs[$type] ?? $allowedDirs['general'];
if (!is_dir($dir)) mkdir($dir, 0755, true);

if (!empty($_FILES['file']['tmp_name'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    if (!in_array($ext, $allowed)) jsonError('Invalid file type. Allowed: ' . implode(', ', $allowed));
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) jsonError('File too large. Max 5MB');

    $name = uniqid() . '.' . $ext;
    $dest = $dir . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) jsonError('Upload failed');

    $url = '/backend/uploads/' . basename($dir) . '/' . $name;
    jsonSuccess(['url' => $url, 'name' => $name]);
}

if (!empty($_POST['url'])) {
    $url = filter_var($_POST['url'], FILTER_VALIDATE_URL);
    if (!$url) jsonError('Invalid URL');
    jsonSuccess(['url' => $url]);
}

jsonError('No file or URL provided');
