<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$validCategories = ['shanti', 'graha', 'sanskar', 'festival', 'other'];
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    $id = $_POST['id'] ?? null;
    $category = $_POST['category'];
    if (!in_array($category, $validCategories, true)) {
        $alertHtml = '<div class="alert-error">अमान्य कोटि</div>';
    } else {
        $data = [
            ':title_ne' => sanitize($_POST['title_ne']),
            ':title_en' => sanitize($_POST['title_en'] ?? ''),
            ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
            ':description_en' => sanitize($_POST['description_en'] ?? ''),
            ':category' => $category,
            ':base_price' => $_POST['base_price'] ?: null,
            ':duration_minutes' => $_POST['duration_minutes'] ?: null,
            ':materials_available' => !empty($_POST['materials_available']) ? 1 : 0,
        ];

        if ($id) {
            $data[':id'] = $id;
            $stmt = $db->prepare("UPDATE pooja_services SET title_ne=:title_ne, title_en=:title_en, description_ne=:description_ne, description_en=:description_en, category=:category, base_price=:base_price, duration_minutes=:duration_minutes, materials_available=:materials_available WHERE id=:id");
            $stmt->execute($data);
            $alertHtml = '<div class="alert-success">सेवा अपडेट गरियो</div>';
        } else {
            $stmt = $db->prepare("INSERT INTO pooja_services (title_ne, title_en, description_ne, description_en, category, base_price, duration_minutes, materials_available) VALUES (:title_ne, :title_en, :description_ne, :description_en, :category, :base_price, :duration_minutes, :materials_available)");
            $stmt->execute($data);
            $alertHtml = '<div class="alert-success">नयाँ सेवा थपियो</div>';
        }
    }
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE pooja_services SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM pooja_services WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">सेवा मेटाइयो</div>';
}

$editService = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM pooja_services WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editService = $stmt->fetch();
}

$services = $db->query("SELECT * FROM pooja_services ORDER BY category, title_ne")->fetchAll();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>पूजा सेवाहरू</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editService ? 'सेवा सम्पादन गर्नुहोस्' : 'नयाँ सेवा थप्नुहोस्' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editService): ?>
            <input type="hidden" name="id" value="<?= $editService['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editService['title_ne'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editService['title_en'] ?? '') ?>"></div>
                <div class="field"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="3" class="form-input"><?= htmlspecialchars($editService['description_ne'] ?? '') ?></textarea></div>
                <div class="field"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="3" class="form-input"><?= htmlspecialchars($editService['description_en'] ?? '') ?></textarea></div>
                <div class="field"><label>कोटि</label>
                    <select name="category" class="form-input">
                        <option value="shanti" <?= ($editService['category'] ?? '') === 'shanti' ? 'selected' : '' ?>>शान्ति</option>
                        <option value="graha" <?= ($editService['category'] ?? '') === 'graha' ? 'selected' : '' ?>>ग्रह</option>
                        <option value="sanskar" <?= ($editService['category'] ?? '') === 'sanskar' ? 'selected' : '' ?>>संस्कार</option>
                        <option value="festival" <?= ($editService['category'] ?? '') === 'festival' ? 'selected' : '' ?>>पर्व</option>
                        <option value="other" <?= ($editService['category'] ?? '') === 'other' ? 'selected' : '' ?>>अन्य</option>
                    </select>
                </div>
                <div class="field"><label>मूल्य (रु)</label><input name="base_price" type="number" step="0.01" class="form-input" value="<?= htmlspecialchars($editService['base_price'] ?? '') ?>"></div>
                <div class="field"><label>अवधि (मिनेट)</label><input name="duration_minutes" type="number" class="form-input" value="<?= htmlspecialchars($editService['duration_minutes'] ?? '') ?>"></div>
                <div class="field" style="display:flex;align-items:center;gap:12px;padding-top:24px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="materials_available" value="1" <?= !empty($editService['materials_available']) ? 'checked' : '' ?>>
                        सामग्री उपलब्ध छ
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_service" class="btn btn-primary"><?= $editService ? 'अपडेट गर्नुहोस्' : 'सेवा थप्नुहोस्' ?></button>
                <?php if ($editService): ?>
                <a href="pooja-services.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>शीर्षक</th><th>कोटि</th><th>मूल्य</th><th>स्थिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td class="td-name"><?= htmlspecialchars($s['title_ne']) ?><?= $s['title_en'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($s['title_en']) . '</small>' : '' ?></td>
                    <td><?= $s['category'] ?></td>
                    <td class="td-amount"><?= $s['base_price'] ? 'रु ' . number_format($s['base_price']) : '—' ?></td>
                    <td><span class="badge badge-<?= $s['is_active'] ? 'confirmed' : 'cancelled' ?>"><?= $s['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
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
                <tr><td colspan="5" class="empty-state">कुनै सेवा छैन</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
