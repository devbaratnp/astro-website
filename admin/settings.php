<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

// Update admin password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE admin_users SET password_hash = :hash WHERE id = :id");
    $stmt->execute([':hash' => $newHash, ':id' => $_SESSION['admin_id']]);
    echo '<div class="alert alert-success">पासवर्ड सफलतापूर्वक परिवर्तन गरियो</div>';
}
?>

<h1>सेटिङ्स</h1>

<div class="form-card" style="max-width:500px">
    <h3>पासवर्ड परिवर्तन गर्नुहोस्</h3>
    <form method="POST">
        <?= csrfField() ?>
        <div class="field">
            <label>नयाँ पासवर्ड</label>
            <input type="password" name="new_password" required minlength="6">
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">परिवर्तन गर्नुहोस्</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>