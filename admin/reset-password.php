<?php
require_once __DIR__ . '/../backend/config/database.php';

if (php_sapi_name() !== 'cli') {
    die("Run via terminal: php admin/reset-password.php\n");
}

echo "Admin Password Reset\n\n";
echo "New password: ";

if (str_starts_with(PHP_OS, 'WIN')) {
    $password = trim(fgets(STDIN));
} else {
    system('stty -echo');
    $password = trim(fgets(STDIN));
    system('stty echo');
}

echo "\nConfirm: ";

if (str_starts_with(PHP_OS, 'WIN')) {
    $confirm = trim(fgets(STDIN));
} else {
    system('stty -echo');
    $confirm = trim(fgets(STDIN));
    system('stty echo');
}

echo "\n";

if ($password !== $confirm || strlen($password) < 4) {
    die("Passwords don't match or too short.\n");
}

$db = Database::getConnection();
$hash = password_hash($password, PASSWORD_DEFAULT);
$db->prepare("UPDATE admin_users SET password_hash = :hash WHERE role = 'admin' LIMIT 1")
   ->execute([':hash' => $hash]);

echo "Password reset successfully.\n";
