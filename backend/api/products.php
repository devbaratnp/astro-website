<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

switch ($method) {
    case 'GET':
        $category = $_GET['category'] ?? '';
        $stmt = $db->prepare("SELECT id, title_ne, title_en, description_ne, description_en, price, compare_price, images, category, stock_status FROM products WHERE is_active = 1" . ($category ? " AND category = :category" : "") . " ORDER BY category, title_ne");
        if ($category) $stmt->execute([':category' => $category]);
        else $stmt->execute();
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['images'] = json_decode($p['images'] ?? '[]', true);
        }
        jsonSuccess($products);
        break;

    default:
        jsonError('Method not allowed', 405);
}
