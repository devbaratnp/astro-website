<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = Database::getConnection();
$type = $_GET['type'] ?? '';
$upcoming = !isset($_GET['past']);

$where = "WHERE e.is_active = 1";
$params = [];

if ($type === 'event' || $type === 'tour') {
    $where .= " AND e.type = :type";
    $params[':type'] = $type;
}

if ($upcoming) {
    $where .= " AND e.date_from >= CURDATE()";
} else {
    $where .= " AND e.date_from < CURDATE()";
}

$stmt = $db->prepare("SELECT e.id, e.type, e.title_ne, e.title_en, e.description_ne, e.description_en, e.date_from, e.date_to, e.time_from, e.location, e.cover_image, e.registration_url, e.contact_person, e.contact_phone FROM events e $where ORDER BY e.date_from ASC LIMIT 50");
$stmt->execute($params);
jsonSuccess($stmt->fetchAll());
