<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        ':service_key' => sanitize($_POST['service_key']),
        ':icon' => sanitize($_POST['icon'] ?? ''),
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
        ':description_en' => sanitize($_POST['description_en'] ?? ''),
        ':sort_order' => (int)($_POST['sort_order'] ?? 0),
        ':is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE services SET service_key=:service_key, icon=:icon, title_ne=:title_ne, title_en=:title_en, description_ne=:description_ne, description_en=:description_en, sort_order=:sort_order, is_active=:is_active WHERE id=:id");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO services (service_key, icon, title_ne, title_en, description_ne, description_en, sort_order, is_active) VALUES (:service_key, :icon, :title_ne, :title_en, :description_ne, :description_en, :sort_order, :is_active)");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ सेवा थपियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">मेटाइयो</div>';
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE services SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

$editService = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editService = $stmt->fetch();
}

$services = $db->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>सेवाहरू</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editService ? 'सम्पादन गर्नुहोस्' : 'नयाँ सेवा' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editService): ?>
            <input type="hidden" name="id" value="<?= $editService['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field"><label>कुञ्जी (key) *</label><input name="service_key" required class="form-input" placeholder="kundali" value="<?= htmlspecialchars($editService['service_key'] ?? '') ?>"></div>
                <div class="field"><label>आइकन</label><input name="icon" class="form-input" placeholder="ChartPolar" value="<?= htmlspecialchars($editService['icon'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editService['title_ne'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editService['title_en'] ?? '') ?>"></div>
                <div class="field"><label>क्रम</label><input name="sort_order" type="number" class="form-input" value="<?= (int)($editService['sort_order'] ?? 0) ?>"></div>
                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= $editService === null || ($editService['is_active'] ?? 1) ? 'checked' : '' ?>>
                        सक्रिय
                    </label>
                </div>
                <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="3" class="form-input"><?= htmlspecialchars($editService['description_ne'] ?? '') ?></textarea></div>
                <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="3" class="form-input"><?= htmlspecialchars($editService['description_en'] ?? '') ?></textarea></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_service" class="btn btn-primary"><?= $editService ? 'अपडेट गर्नुहोस्' : 'सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editService): ?>
                <a href="services.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>कुञ्जी</th><th>शीर्षक</th><th>आइकन</th><th>क्रम</th><th>स्थिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td><code style="font-size:.8rem"><?= htmlspecialchars($s['service_key']) ?></code></td>
                    <td class="td-name"><?= htmlspecialchars($s['title_ne']) ?><?= $s['title_en'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($s['title_en']) . '</small>' : '' ?></td>
                    <td><?= htmlspecialchars($s['icon'] ?? '-') ?></td>
                    <td><?= (int)$s['sort_order'] ?></td>
                    <td><span class="badge badge-<?= $s['is_active'] ? 'confirmed' : 'muted' ?>"><?= $s['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
                    <td>
                        <div class="action-form">
                            <a href="?edit=<?= $s['id'] ?>" class="btn-sm">सम्पादन</a>
                            <a href="?toggle=<?= $s['id'] ?>&<?= csrfQuery() ?>" class="btn-sm"><?= $s['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
                            <a href="?delete=<?= $s['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($services)): ?>
                <tr><td colspan="6" class="empty-state">कुनै सेवा छैन</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
