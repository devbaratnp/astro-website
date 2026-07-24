<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $id = $_POST['id'] ?? null;
    $username = sanitize($_POST['username']);
    $display_name = sanitize($_POST['display_name']);
    $role = in_array($_POST['role'] ?? '', ['admin', 'editor']) ? $_POST['role'] : 'editor';
    $password = $_POST['password'] ?? '';

    if ($id) {
        $check = $db->prepare("SELECT id FROM admin_users WHERE username = :u AND id != :id");
        $check->execute([':u' => $username, ':id' => $id]);
        if ($check->fetchColumn()) {
            $alertHtml = '<div class="alert-error">यो प्रयोगकर्ता नाम पहिले नै छ</div>';
        } else {
            $sql = "UPDATE admin_users SET username = :username, display_name = :display_name, role = :role" . ($password ? ", password_hash = :hash" : "") . " WHERE id = :id";
            $params = [':username' => $username, ':display_name' => $display_name, ':role' => $role, ':id' => $id];
            if ($password) $params[':hash'] = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare($sql)->execute($params);
            $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
        }
    } else {
        if (strlen($password) < 6) {
            $alertHtml = '<div class="alert-error">पासवर्ड कम्तीमा ६ क्यारेक्टर हुनुपर्छ</div>';
        } else {
            $check = $db->prepare("SELECT id FROM admin_users WHERE username = :u");
            $check->execute([':u' => $username]);
            if ($check->fetchColumn()) {
                $alertHtml = '<div class="alert-error">यो प्रयोगकर्ता नाम पहिले नै छ</div>';
            } else {
                $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash, display_name, role) VALUES (:username, :hash, :display_name, :role)");
                $stmt->execute([':username' => $username, ':hash' => password_hash($password, PASSWORD_DEFAULT), ':display_name' => $display_name, ':role' => $role]);
                $alertHtml = '<div class="alert-success">नयाँ प्रयोगकर्ता थपियो</div>';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $delId = (int)$_GET['delete'];
    if ($delId === (int)$_SESSION['admin_id']) {
        $alertHtml = '<div class="alert-error">आफूलाई मेटाउन सकिँदैन</div>';
    } else {
        $stmt = $db->prepare("DELETE FROM admin_users WHERE id = :id");
        $stmt->execute([':id' => $delId]);
        $alertHtml = '<div class="alert-success">मेटाइयो</div>';
    }
}

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT id, username, display_name, role FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editUser = $stmt->fetch();
}

$users = $db->query("SELECT id, username, display_name, role, created_at FROM admin_users ORDER BY created_at ASC")->fetchAll();
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>प्रयोगकर्ता व्यवस्थापन</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editUser ? 'सम्पादन गर्नुहोस्' : 'नयाँ प्रयोगकर्ता' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editUser): ?>
            <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field"><label>प्रयोगकर्ता नाम *</label><input name="username" required class="form-input" value="<?= htmlspecialchars($editUser['username'] ?? '') ?>"></div>
                <div class="field"><label>नाम *</label><input name="display_name" required class="form-input" value="<?= htmlspecialchars($editUser['display_name'] ?? '') ?>"></div>
                <div class="field">
                    <label>भूमिका</label>
                    <select name="role" class="form-input">
                        <option value="admin" <?= $editUser && $editUser['role'] === 'admin' ? 'selected' : '' ?>>प्रशासक</option>
                        <option value="editor" <?= $editUser && $editUser['role'] === 'editor' ? 'selected' : '' ?>>सम्पादक</option>
                    </select>
                </div>
                <div class="field"><label><?= $editUser ? 'नयाँ पासवर्ड (खाली राखेमा परिवर्तन हुँदैन)' : 'पासवर्ड *' ?></label><input name="password" type="text" class="form-input" placeholder="कम्तीमा ६ क्यारेक्टर" <?= $editUser ? '' : 'required' ?>></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_user" class="btn btn-primary"><?= $editUser ? 'अपडेट गर्नुहोस्' : 'सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editUser): ?>
                <a href="users.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>प्रयोगकर्ता</th><th>नाम</th><th>भूमिका</th><th>मिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><code style="font-size:.8rem"><?= htmlspecialchars($u['username']) ?></code></td>
                    <td class="td-name"><?= htmlspecialchars($u['display_name']) ?></td>
                    <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'gold' : 'info' ?>"><?= $u['role'] ?></span></td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <div class="action-form">
                            <a href="?edit=<?= $u['id'] ?>" class="btn-sm">सम्पादन</a>
                            <?php if ((int)$u['id'] !== (int)$_SESSION['admin_id']): ?>
                            <a href="?delete=<?= $u['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
