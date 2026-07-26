<?php
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'),'httponly'=>true,'samesite'=>'Strict']);
session_start();
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

$step = 1;
$username = $_POST['username'] ?? ($_GET['user'] ?? '');
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $db = Database::getConnection();

    if (isset($_POST['verify_user'])) {
        $stmt = $db->prepare("SELECT id, username, display_name FROM admin_users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => trim($_POST['username'])]);
        $user = $stmt->fetch();

        if ($user) {
            $step = 2;
            $username = $user['username'];
            $displayName = $user['display_name'];
        } else {
            $error = 'प्रयोगकर्ता नाम फेला परेन';
        }
    }

    if (isset($_POST['reset_password'])) {
        $stmt = $db->prepare("SELECT id, password_hash FROM admin_users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => trim($_POST['username'])]);
        $admin = $stmt->fetch();

        if (!$admin) {
            $error = 'प्रयोगकर्ता नाम फेला परेन';
        } elseif (!password_verify($_POST['old_password'] ?? '', $admin['password_hash'])) {
            $error = 'पुरानो पासवर्ड मिलेन';
        } elseif (strlen($_POST['new_password'] ?? '') < 6) {
            $error = 'नयाँ पासवर्ड कम्तिमा ६ क्यारेक्टर हुनुपर्छ';
        } elseif ($_POST['new_password'] !== ($_POST['confirm_password'] ?? '')) {
            $error = 'नयाँ पासवर्ड र पुष्टि पासवर्ड मिलेन';
        } else {
            $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $db->prepare("UPDATE admin_users SET password_hash = :hash WHERE id = :id")
                ->execute([':hash' => $newHash, ':id' => $admin['id']]);
            $success = 'पासवर्ड सफलतापूर्वक परिवर्तन गरियो। <a href="' . BASE_URL . '/admin/index.php">लगइन गर्नुहोस्</a>';
            $step = 3;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>पासवर्ड भुल्नुभयो? — श्रीहरि ज्योतिष</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <svg width="64" height="64" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="30" fill="#54121b"/>
                <text x="32" y="40" text-anchor="middle" fill="#e2bd72" font-size="28" font-family="serif">ॐ</text>
            </svg>
        </div>

        <?php if ($step === 1): ?>
            <h1>पासवर्ड भुल्नुभयो?</h1>
            <p class="login-subtitle">आफ्नो प्रयोगकर्ता नाम प्रविष्ट गर्नुहोस्</p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?= csrfField() ?>
                <div class="form-field">
                    <label class="form-label">प्रयोगकर्ता नाम</label>
                    <input class="form-input" name="username" required autocomplete="username"
                           placeholder="admin" value="<?= htmlspecialchars($username) ?>">
                </div>
                <button class="btn btn-primary" type="submit" name="verify_user">अर्को</button>
            </form>

        <?php elseif ($step === 2): ?>
            <h1>पासवर्ड रिसेट</h1>
            <p class="login-subtitle">प्रयोगकर्ता: <strong><?= htmlspecialchars($username) ?></strong></p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                <div class="form-field">
                    <label class="form-label">पुरानो पासवर्ड</label>
                    <input type="password" name="old_password" required class="form-input">
                </div>
                <div class="form-field">
                    <label class="form-label">नयाँ पासवर्ड</label>
                    <input type="password" name="new_password" required minlength="6" class="form-input">
                </div>
                <div class="form-field">
                    <label class="form-label">नयाँ पासवर्ड पुष्टि गर्नुहोस्</label>
                    <input type="password" name="confirm_password" required minlength="6" class="form-input">
                </div>
                <button class="btn btn-primary" type="submit" name="reset_password">पासवर्ड परिवर्तन गर्नुहोस्</button>
            </form>

        <?php elseif ($step === 3): ?>
            <h1>पासवर्ड परिवर्तन भयो</h1>
            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/admin/index.php" class="back-link">← लगइनमा फर्कनुहोस्</a>
    </div>
</body>
</html>
