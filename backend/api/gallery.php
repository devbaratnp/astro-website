<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = Database::getConnection();
$type = $_GET['type'] ?? '';

$where = "WHERE is_active = 1";
$params = [];

if ($type === 'image' || $type === 'video' || $type === 'audio') {
    $where .= " AND type = :type";
    $params[':type'] = $type;
}

$stmt = $db->prepare("SELECT id, type, title_ne, title_en, url, thumbnail, embed_url, source FROM gallery_items $where ORDER BY sort_order ASC, created_at DESC LIMIT 100");
$stmt->execute($params);
jsonSuccess($stmt->fetchAll());
