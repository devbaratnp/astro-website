<?php
session_start();
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('admin_remember', '', time() - 3600, '/');
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';
$rememberedUser = $_COOKIE['admin_remember'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['display_name'];
        $_SESSION['admin_role'] = $user['role'];

        if (!empty($_POST['remember'])) {
            setcookie('admin_remember', $_POST['username'], time() + 86400 * 30, '/');
        } else {
            setcookie('admin_remember', '', time() - 3600, '/');
        }

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <svg width="64" height="64" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="30" fill="#54121b"/>
                <text x="32" y="40" text-anchor="middle" fill="#e2bd72" font-size="28" font-family="serif">ॐ</text>
            </svg>
        </div>
        <h1>प्रशासक लगइन</h1>
        <p class="login-subtitle">श्रीहरि ज्योतिष प्रशासन प्रणाली</p>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField() ?>
            <div class="form-field">
                <label class="form-label">प्रयोगकर्ता नाम</label>
                <input class="form-input" name="username" required autocomplete="username" placeholder="admin"
                       value="<?= htmlspecialchars($rememberedUser) ?>">
            </div>
            <div class="form-field">
                <label class="form-label">पासवर्ड</label>
                <div class="password-wrap">
                    <input class="form-input" type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••" id="loginPassword">
                    <button type="button" class="password-toggle" id="pwToggle"
                            aria-label="पासवर्ड देखानुहोस् / लुकाउनुहोस्">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path id="eyeIcon" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle id="eyePupil" cx="12" cy="12" r="3"/>
                            <line id="eyeSlash1" x1="1" y1="1" x2="23" y2="23" style="display:none"/>
                            <line id="eyeSlash2" x1="23" y1="1" x2="1" y2="23" style="display:none"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="login-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" value="1"
                        <?= $rememberedUser ? 'checked' : '' ?>>
                    मलाई सम्झनुहोस्
                </label>
            </div>
            <button class="btn btn-primary" type="submit">लगइन</button>
        </form>

        <a href="<?= BASE_URL ?>" class="back-link">← Back to website</a>
    </div>

<script>
(function(){
    var toggle = document.getElementById('pwToggle');
    var pw = document.getElementById('loginPassword');
    if (toggle && pw) {
        toggle.addEventListener('click', function(){
            var show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            document.getElementById('eyeSlash1').style.display = show ? 'block' : 'none';
            document.getElementById('eyeSlash2').style.display = show ? 'block' : 'none';
            document.getElementById('eyeIcon').style.display = show ? 'none' : 'block';
            document.getElementById('eyePupil').style.display = show ? 'none' : 'block';
            toggle.setAttribute('aria-label', show ? 'पासवर्ड लुकाउनुहोस्' : 'पासवर्ड देखानुहोस्');
        });
    }
})();
</script>
</body>
</html>
