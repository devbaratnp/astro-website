<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_testimonial'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        ':name' => sanitize($_POST['name']),
        ':title' => sanitize($_POST['title'] ?? ''),
        ':content' => sanitize($_POST['content']),
        ':rating' => (int)($_POST['rating'] ?? 5),
        ':photo' => sanitize($_POST['photo'] ?? ''),
        ':location' => sanitize($_POST['location'] ?? ''),
        ':sort_order' => (int)($_POST['sort_order'] ?? 0),
        ':is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE testimonials SET name=:name, title=:title, content=:content, rating=:rating, photo=:photo, location=:location, sort_order=:sort_order, is_active=:is_active WHERE id=:id");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO testimonials (name, title, content, rating, photo, location, sort_order, is_active) VALUES (:name, :title, :content, :rating, :photo, :location, :sort_order, :is_active)");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ प्रशंसापत्र थपियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM testimonials WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">मेटाइयो</div>';
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

$editTestimonial = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editTestimonial = $stmt->fetch();
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>प्रशंसापत्र</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editTestimonial ? 'सम्पादन गर्नुहोस्' : 'नयाँ प्रशंसापत्र' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editTestimonial): ?>
            <input type="hidden" name="id" value="<?= $editTestimonial['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field"><label>नाम *</label><input name="name" required class="form-input" value="<?= htmlspecialchars($editTestimonial['name'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक</label><input name="title" class="form-input" placeholder="ग्राहक / भक्त" value="<?= htmlspecialchars($editTestimonial['title'] ?? '') ?>"></div>
                <div class="field"><label>मूल्याङ्कन</label>
                    <select name="rating" class="form-input">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>" <?= ($editTestimonial['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= $i ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="field"><label>स्थान</label><input name="location" class="form-input" value="<?= htmlspecialchars($editTestimonial['location'] ?? '') ?>"></div>
                <div class="field"><label>क्रम</label><input name="sort_order" type="number" class="form-input" value="<?= (int)($editTestimonial['sort_order'] ?? 0) ?>"></div>
                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= $editTestimonial === null || ($editTestimonial['is_active'] ?? 1) ? 'checked' : '' ?>>
                        सक्रिय
                    </label>
                </div>
                <div class="field">
                    <label>फोटो</label>
                    <div class="file-upload-wrap" data-field="photo">
                        <div class="file-zone" data-field="photo" role="button" tabindex="0" aria-label="Upload photo">
                            <?php if (!empty($editTestimonial['photo'])): ?>
                            <img src="<?= htmlspecialchars($editTestimonial['photo']) ?>" alt="preview" class="file-preview">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($editTestimonial['photo'])): ?>
                        <button type="button" class="file-clear" data-field="photo" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="photo" value="<?= htmlspecialchars($editTestimonial['photo'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="field full"><label>सामग्री *</label><textarea name="content" required rows="4" class="form-input"><?= htmlspecialchars($editTestimonial['content'] ?? '') ?></textarea></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_testimonial" class="btn btn-primary"><?= $editTestimonial ? 'अपडेट गर्नुहोस्' : 'सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editTestimonial): ?>
                <a href="testimonials.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>नाम</th><th>सामग्री</th><th>मूल्याङ्कन</th><th>स्थान</th><th>क्रम</th><th>स्थिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td class="td-name"><?= htmlspecialchars($t['name']) ?><?= $t['title'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($t['title']) . '</small>' : '' ?></td>
                    <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars(mb_substr($t['content'], 0, 100)) ?></td>
                    <td><span style="color:var(--gold)"><?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?></span></td>
                    <td><?= htmlspecialchars($t['location'] ?? '-') ?></td>
                    <td><?= (int)$t['sort_order'] ?></td>
                    <td><span class="badge badge-<?= $t['is_active'] ? 'confirmed' : 'muted' ?>"><?= $t['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
                    <td>
                        <div class="action-form">
                            <a href="?edit=<?= $t['id'] ?>" class="btn-sm">सम्पादन</a>
                            <a href="?toggle=<?= $t['id'] ?>&<?= csrfQuery() ?>" class="btn-sm"><?= $t['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
                            <a href="?delete=<?= $t['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($testimonials)): ?>
                <tr><td colspan="7" class="empty-state">कुनै प्रशंसापत्र छैन</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
        fd.append('type', 'testimonial');

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
