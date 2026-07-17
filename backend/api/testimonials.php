<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT id, name, title, content, rating, photo, location FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 20");
jsonSuccess($stmt->fetchAll());
