<?php
require_once __DIR__ . '/../backend/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="ne">
    <head>
        <meta charset="utf-8">
        <title>पासवर्ड रिसेट — श्रीहरि ज्योतिष</title>
        <link rel="stylesheet" href="../assets/admin.css">
    </head>
    <body class="login-page">
        <div class="login-box">
            <h1>पासवर्ड रिसेट</h1>
            <form method="POST">
                <div class="field"><label>नयाँ पासवर्ड</label><input name="password" type="text" required minlength="4"></div>
                <div class="field"><label>पुष्टि गर्नुहोस्</label><input name="confirm" type="text" required></div>
                <button class="btn btn-primary" type="submit" style="width:100%">रिसेट गर्नुहोस्</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if ($password !== $confirm || strlen($password) < 4) {
    echo '<div class="login-box" style="margin-top:2rem"><div class="alert alert-error">पासवर्ड मिलेन वा धेरै छोटो छ</div></div>';
    exit;
}

$db = Database::getConnection();
$hash = password_hash($password, PASSWORD_DEFAULT);
$db->prepare("UPDATE admin_users SET password_hash = :hash WHERE role = 'admin' LIMIT 1")
   ->execute([':hash' => $hash]);

echo '<div class="login-box" style="margin-top:2rem"><div class="alert alert-success">पासवर्ड रिसेट गरियो। <a href="index.php">लगइन गर्नुहोस्</a></div></div>';
