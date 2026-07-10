<?php
session_start();
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/includes/config.php';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['display_name'];
        $_SESSION['admin_role'] = $user['role'];
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    } else {
        $error = 'गलत प्रयोगकर्ता नाम वा पासवर्ड';
    }
}
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>प्रशासक लगइन — श्रीहरि ज्योतिष</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?= BASE_URL ?>/assets/logo.svg" alt="श्रीहरि ज्योतिष">
        </div>
        <h1>प्रशासक लगइन</h1>
        <p class="login-subtitle">श्रीहरि ज्योतिष प्रशासन प्रणाली</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label>प्रयोगकर्ता नाम</label>
                <input name="username" required autocomplete="username" placeholder="admin">
            </div>
            <div class="field">
                <label>पासवर्ड</label>
                <div class="password-wrap">
                    <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" id="loginPassword">
                    <button type="button" class="password-toggle" onclick="var p=document.getElementById('loginPassword');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'👁':'🙈'" aria-label="पासवर्ड देखानुहोस्">👁</button>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">लगइन</button>
        </form>
    </div>
</body>
</html>
