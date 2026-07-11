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
if (!in_array($input['booking_type'], ['appointment','pooja','reward'], true)) jsonError('Invalid booking type');
if ((int)$input['booking_id'] < 1 || (float)$input['amount'] <= 0) jsonError('Invalid booking or amount');

$screenshotPath = null;
if (!empty($input['screenshot'])) {
    $uploadDir = __DIR__ . '/../uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $data = base64_decode($input['screenshot'], true);
    if ($data !== false && strlen($data) <= 5 * 1024 * 1024) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($data);
        $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!isset($extensions[$mime])) jsonError('Invalid screenshot type');
        $filename = 'payment_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        file_put_contents($uploadDir . $filename, $data);
        $screenshotPath = '/backend/uploads/payments/' . $filename;
    } else jsonError('Screenshot is invalid or larger than 5 MB');
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
