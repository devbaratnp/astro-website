<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = getJsonInput();

$error = validateRequired($input, ['booking_type', 'booking_id', 'user_name', 'user_phone', 'amount', 'method', 'transaction_ref']);
if ($error) {
    jsonError($error);
}

if (!in_array($input['method'], ['esewa', 'khalti', 'imepay', 'bank'])) {
    jsonError('अमान्य भुक्तानी विधि');
}

$screenshotPath = null;
if (!empty($input['screenshot'])) {
    $uploadDir = __DIR__ . '/../../uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
    $data = base64_decode($input['screenshot']);
    if ($data !== false) {
        file_put_contents($uploadDir . $filename, $data);
        $screenshotPath = '/uploads/payments/' . $filename;
    }
}

$stmt = $db->prepare("
    INSERT INTO payments (booking_type, booking_id, user_name, user_phone, amount, method, transaction_ref, screenshot_path, status)
    VALUES (:booking_type, :booking_id, :user_name, :user_phone, :amount, :method, :transaction_ref, :screenshot, 'pending')
");

$stmt->execute([
    ':booking_type' => $input['booking_type'],
    ':booking_id' => (int)$input['booking_id'],
    ':user_name' => sanitize($input['user_name']),
    ':user_phone' => sanitize($input['user_phone']),
    ':amount' => (float)$input['amount'],
    ':method' => $input['method'],
    ':transaction_ref' => sanitize($input['transaction_ref']),
    ':screenshot' => $screenshotPath,
]);

jsonSuccess(['id' => $db->lastInsertId()], 'भुक्तानी विवरण प्राप्त भयो। प्रशासकले पुष्टि गरेपछि सूचित गरिनेछ।');
