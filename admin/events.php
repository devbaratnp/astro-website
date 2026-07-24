<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';
$filter = $_GET['type'] ?? 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        ':type' => $_POST['type'] === 'tour' ? 'tour' : 'event',
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
        ':description_en' => sanitize($_POST['description_en'] ?? ''),
        ':date_from' => $_POST['date_from'] ?: null,
        ':date_to' => $_POST['date_to'] ?: null,
        ':time_from' => $_POST['time_from'] ?: null,
        ':location' => sanitize($_POST['location'] ?? ''),
        ':cover_image' => sanitize($_POST['cover_image'] ?? ''),
        ':registration_url' => sanitize($_POST['registration_url'] ?? ''),
        ':contact_person' => sanitize($_POST['contact_person'] ?? ''),
        ':contact_phone' => sanitize($_POST['contact_phone'] ?? ''),
        ':is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE events SET type=:type, title_ne=:title_ne, title_en=:title_en, description_ne=:description_ne, description_en=:description_en, date_from=:date_from, date_to=:date_to, time_from=:time_from, location=:location, cover_image=:cover_image, registration_url=:registration_url, contact_person=:contact_person, contact_phone=:contact_phone, is_active=:is_active WHERE id=:id");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO events (type, title_ne, title_en, description_ne, description_en, date_from, date_to, time_from, location, cover_image, registration_url, contact_person, contact_phone, is_active) VALUES (:type, :title_ne, :title_en, :description_ne, :description_en, :date_from, :date_to, :time_from, :location, :cover_image, :registration_url, :contact_person, :contact_phone, :is_active)");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ कार्यक्रम थपियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">मेटाइयो</div>';
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE events SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

$editEvent = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editEvent = $stmt->fetch();
}

$where = $filter === 'all' ? '' : "WHERE type = :type";
$params = $filter === 'all' ? [] : [':type' => $filter];
$stmt = $db->prepare("SELECT * FROM events $where ORDER BY date_from DESC LIMIT 100");
$stmt->execute($params);
$events = $stmt->fetchAll();
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>कार्यक्रम तथा यात्रा</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editEvent ? 'सम्पादन गर्नुहोस्' : 'नयाँ कार्यक्रम' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editEvent): ?>
            <input type="hidden" name="id" value="<?= $editEvent['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field">
                    <label>प्रकार *</label>
                    <select name="type" class="form-input">
                        <option value="event" <?= $editEvent && $editEvent['type'] === 'event' ? 'selected' : '' ?>>प्रवचन तथा कार्यक्रम</option>
                        <option value="tour" <?= $editEvent && $editEvent['type'] === 'tour' ? 'selected' : '' ?>>धार्मिक यात्रा</option>
                    </select>
                </div>
                <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editEvent['title_ne'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editEvent['title_en'] ?? '') ?>"></div>
                <div class="field"><label>मिति (बाट) *</label><input name="date_from" type="date" required class="form-input" value="<?= htmlspecialchars($editEvent['date_from'] ?? '') ?>"></div>
                <div class="field"><label>मिति (सम्म)</label><input name="date_to" type="date" class="form-input" value="<?= htmlspecialchars($editEvent['date_to'] ?? '') ?>"></div>
                <div class="field"><label>समय</label><input name="time_from" type="time" class="form-input" value="<?= htmlspecialchars($editEvent['time_from'] ?? '') ?>"></div>
                <div class="field"><label>स्थान</label><input name="location" class="form-input" placeholder="काठमाडौं" value="<?= htmlspecialchars($editEvent['location'] ?? '') ?>"></div>
                <div class="field">
                    <label>कभर छवि</label>
                    <div class="file-upload-wrap" data-field="cover_image">
                        <div class="file-zone" data-field="cover_image" role="button" tabindex="0" aria-label="Upload cover image">
                            <?php if (!empty($editEvent['cover_image'])): ?>
                            <img src="<?= htmlspecialchars($editEvent['cover_image']) ?>" alt="preview" class="file-preview">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($editEvent['cover_image'])): ?>
                        <button type="button" class="file-clear" data-field="cover_image" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="cover_image" value="<?= htmlspecialchars($editEvent['cover_image'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="field"><label>दर्ता URL</label><input name="registration_url" class="form-input" placeholder="https://..." value="<?= htmlspecialchars($editEvent['registration_url'] ?? '') ?>"></div>
                <div class="field"><label>सम्पर्क व्यक्ति</label><input name="contact_person" class="form-input" value="<?= htmlspecialchars($editEvent['contact_person'] ?? '') ?>"></div>
                <div class="field"><label>सम्पर्क फोन</label><input name="contact_phone" class="form-input" value="<?= htmlspecialchars($editEvent['contact_phone'] ?? '') ?>"></div>
                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= $editEvent === null || ($editEvent['is_active'] ?? 1) ? 'checked' : '' ?>>
                        सक्रिय
                    </label>
                </div>
                <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="4" class="form-input"><?= htmlspecialchars($editEvent['description_ne'] ?? '') ?></textarea></div>
                <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="4" class="form-input"><?= htmlspecialchars($editEvent['description_en'] ?? '') ?></textarea></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_event" class="btn btn-primary"><?= $editEvent ? 'अपडेट गर्नुहोस्' : 'सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editEvent): ?>
                <a href="events.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div>
        <div class="tab-filter-bar">
            <a href="events.php" class="tab-filter <?= $filter === 'all' ? 'active' : '' ?>">सबै</a>
            <a href="events.php?type=event" class="tab-filter <?= $filter === 'event' ? 'active' : '' ?>">कार्यक्रम</a>
            <a href="events.php?type=tour" class="tab-filter <?= $filter === 'tour' ? 'active' : '' ?>">यात्रा</a>
        </div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>शीर्षक</th><th>प्रकार</th><th>मिति</th><th>स्थान</th><th>स्थिति</th><th>कार्य</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $e): ?>
                    <tr>
                        <td class="td-name">
                            <?= htmlspecialchars($e['title_ne']) ?>
                            <?= $e['title_en'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($e['title_en']) . '</small>' : '' ?>
                        </td>
                        <td><span class="badge badge-<?= $e['type'] === 'tour' ? 'gold' : 'info' ?>"><?= $e['type'] === 'tour' ? 'यात्रा' : 'कार्यक्रम' ?></span></td>
                        <td><?= $e['date_from'] ? htmlspecialchars(substr($e['date_from'], 0, 10)) : '-' ?><?= $e['date_to'] ? ' — ' . htmlspecialchars(substr($e['date_to'], 0, 10)) : '' ?></td>
                        <td><?= htmlspecialchars($e['location'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $e['is_active'] ? 'confirmed' : 'muted' ?>"><?= $e['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
                        <td>
                            <div class="action-form">
                                <a href="?edit=<?= $e['id'] ?>" class="btn-sm">सम्पादन</a>
                                <a href="?toggle=<?= $e['id'] ?>&<?= csrfQuery() ?>" class="btn-sm"><?= $e['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
                                <a href="?delete=<?= $e['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($events)): ?>
                    <tr><td colspan="6" class="empty-state">कुनै कार्यक्रम छैन</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function(){
    var API_BASE = '<?= BASE_URL ?>/backend/api';

    document.addEventListener('click', function(e) {
        var clearBtn = e.target.closest('.file-clear');
        if (clearBtn) {
            var field = clearBtn.dataset.field;
            var wrap = document.querySelector('.file-upload-wrap[data-field="' + field + '"]');
            if (!wrap) return;
            wrap.querySelector('.file-zone').innerHTML = '<span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>';
            wrap.querySelector('.file-hidden').value = '';
            var btn = wrap.querySelector('.file-clear');
            if (btn) btn.remove();
        }
    });

    document.addEventListener('change', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone) return;
        var fileInput = zone.querySelector('input[type="file"]');
        if (fileInput && e.target === fileInput) {
            uploadImage(fileInput, zone);
        }
    });

    document.addEventListener('dragover', function(e) {
        var zone = e.target.closest('.file-zone');
        if (zone) { e.preventDefault(); zone.classList.add('drag-over'); }
    });

    document.addEventListener('dragleave', function(e) {
        var zone = e.target.closest('.file-zone');
        if (zone) zone.classList.remove('drag-over');
    });

    document.addEventListener('drop', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone) return;
        e.preventDefault();
        zone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        if (file) uploadImageFile(file, zone.dataset.field);
    });

    document.addEventListener('click', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone) return;
        var input = zone.querySelector('input[type="file"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp,image/gif,image/svg+xml';
            input.style.display = 'none';
            zone.appendChild(input);
        }
        input.click();
    });

    function uploadImage(input, zone) {
        var file = input.files[0];
        if (!file) return;
        uploadImageFile(file, zone.dataset.field);
    }

    function uploadImageFile(file, field) {
        var zone = document.querySelector('.file-zone[data-field="' + field + '"]');
        if (!zone) return;
        zone.innerHTML = '<span><span class="upload-icon">⏳</span>Uploading...</span>';

        var fd = new FormData();
        fd.append('file', file);
        fd.append('type', 'event');

        fetch(API_BASE + '/upload.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.success) throw new Error(d.message || 'Upload failed');
            var url = d.data.url;
            zone.innerHTML = '<img src="' + url + '" alt="preview" class="file-preview">';
            var wrap = document.querySelector('.file-upload-wrap[data-field="' + field + '"]');
            if (wrap) {
                wrap.querySelector('.file-hidden').value = url;
                if (!wrap.querySelector('.file-clear')) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'file-clear';
                    btn.dataset.field = field;
                    btn.setAttribute('aria-label', 'Clear image');
                    btn.textContent = '✕';
                    wrap.appendChild(btn);
                }
            }
        })
        .catch(function(e) {
            zone.innerHTML = '<span><span class="upload-icon">⚠️</span>Upload failed. Try again.</span>';
        });
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
