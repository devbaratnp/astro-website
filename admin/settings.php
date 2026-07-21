<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

// Update admin password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($_POST['current_password'] ?? '', $admin['password_hash'])) {
        echo '<div class="alert alert-danger">हालको पासवर्ड मिलेन</div>';
    } elseif (strlen($_POST['new_password'] ?? '') < 6) {
        echo '<div class="alert alert-danger">नयाँ पासवर्ड कम्तिमा ६ क्यारेक्टर हुनुपर्छ</div>';
    } elseif ($_POST['new_password'] !== ($_POST['confirm_password'] ?? '')) {
        echo '<div class="alert alert-danger">नयाँ पासवर्ड र पुष्टि पासवर्ड मिलेन</div>';
    } else {
        $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE admin_users SET password_hash = :hash WHERE id = :id");
        $stmt->execute([':hash' => $newHash, ':id' => $_SESSION['admin_id']]);
        echo '<div class="alert alert-success">पासवर्ड सफलतापूर्वक परिवर्तन गरियो</div>';
    }
}
?>

<h1>सेटिङ्स</h1>

<div class="form-card" style="max-width:500px">
    <h3>पासवर्ड परिवर्तन गर्नुहोस्</h3>
    <form method="POST">
        <?= csrfField() ?>
        <div class="field">
            <label>हालको पासवर्ड</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="field">
            <label>नयाँ पासवर्ड</label>
            <input type="password" name="new_password" required minlength="6">
        </div>
        <div class="field">
            <label>नयाँ पासवर्ड पुष्टि गर्नुहोस्</label>
            <input type="password" name="confirm_password" required minlength="6">
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">परिवर्तन गर्नुहोस्</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>