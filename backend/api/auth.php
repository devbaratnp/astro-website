<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../middleware/validate.php';

$method = $_SERVER['REQUEST_METHOD'];
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'),'httponly'=>true,'samesite'=>'Strict']);
if (session_status() === PHP_SESSION_NONE) session_start();

if ($method === 'GET') {
    if (isset($_GET['logout'])) {
        session_destroy();
        jsonSuccess(null, 'Logged out');
    }
    if (empty($_SESSION['admin_id'])) jsonError('Unauthenticated', 401);
    jsonSuccess(['user' => [
        'id' => $_SESSION['admin_id'],
        'display_name' => $_SESSION['admin_name'],
        'role' => $_SESSION['admin_role'],
    ]]);
}

if ($method !== 'POST') {
    jsonError('Method not allowed', 405);
}

$db = Database::getConnection();

$input = getJsonInput();
$error = validateRequired($input, ['username', 'password']);
if ($error) jsonError($error);

$stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $input['username']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_name'] = $user['display_name'];
    $_SESSION['admin_role'] = $user['role'];
    jsonSuccess([
        'user' => [
            'id' => $user['id'],
            'display_name' => $user['display_name'],
            'role' => $user['role'],
        ]
    ], 'Login successful');
} else {
    jsonError('गलत प्रयोगकर्ता नाम वा पासवर्ड', 401);
}
