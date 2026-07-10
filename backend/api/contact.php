<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();
$error = validateRequired($input, ['name', 'subject', 'message']);
if ($error) jsonError($error);

$db = Database::getConnection();
$stmt = $db->prepare("
    INSERT INTO contact_messages (name, phone, email, subject, message)
    VALUES (:name, :phone, :email, :subject, :message)
");
$stmt->execute([
    ':name' => sanitize($input['name']),
    ':phone' => sanitize($input['phone'] ?? ''),
    ':email' => sanitize($input['email'] ?? ''),
    ':subject' => sanitize($input['subject']),
    ':message' => sanitize($input['message']),
]);

jsonSuccess(['id' => $db->lastInsertId()], 'तपाईंको सन्देश प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।');