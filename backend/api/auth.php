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
$error = validateRequired($input, ['username', 'password']);
if ($error) jsonError($error);

$stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $input['username']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'])) {
    $token = bin2hex(random_bytes(32));
    jsonSuccess([
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'display_name' => $user['display_name'],
            'role' => $user['role'],
        ]
    ], 'Login successful');
} else {
    jsonError('गलत प्रयोगकर्ता नाम वा पासवर्ड', 401);
}