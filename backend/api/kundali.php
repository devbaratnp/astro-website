<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../lib/Astrology.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();
$error = validateRequired($input, ['name']);
if ($error) {
    jsonError($error);
}

$astrology = new Astrology(
    $input['birth_date'] ?? null,
    $input['birth_time'] ?? null,
    $input['birth_place'] ?? null
);

$details = $astrology->getBasicDetails();

// Save inquiry
$db = Database::getConnection();
$stmt = $db->prepare("
    INSERT INTO appointments (name, phone, service_type, birth_date, birth_time, birth_place, message, status)
    VALUES (:name, :phone, 'kundali', :birth_date, :birth_time, :birth_place, :message, 'pending')
");
$stmt->execute([
    ':name' => sanitize($input['name']),
    ':phone' => sanitize($input['phone'] ?? ''),
    ':birth_date' => $input['birth_date'] ?? null,
    ':birth_time' => $input['birth_time'] ?? null,
    ':birth_place' => sanitize($input['birth_place'] ?? ''),
    ':message' => 'स्वचालित कुण्डली हेरेपछि परामर्श अनुरोध',
]);

jsonSuccess([
    'kundali' => $details,
    'message' => 'तपाईंको आधारभूत कुण्डली विवरण तयार छ। विस्तृत परामर्शका लागि कृपया सम्पर्क गर्नुहोस्।'
]);
