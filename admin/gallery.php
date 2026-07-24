<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';
$filter = $_GET['type'] ?? 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_gallery'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        ':type' => $_POST['type'] ?: 'image',
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':url' => sanitize($_POST['url'] ?? ''),
        ':thumbnail' => sanitize($_POST['thumbnail'] ?? ''),
        ':embed_url' => sanitize($_POST['embed_url'] ?? ''),
        ':source' => sanitize($_POST['source'] ?? ''),
        ':sort_order' => (int)($_POST['sort_order'] ?? 0),
        ':is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE gallery_items SET type=:type, title_ne=:title_ne, title_en=:title_en, url=:url, thumbnail=:thumbnail, embed_url=:embed_url, source=:source, sort_order=:sort_order, is_active=:is_active WHERE id=:id");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO gallery_items (type, title_ne, title_en, url, thumbnail, embed_url, source, sort_order, is_active) VALUES (:type, :title_ne, :title_en, :url, :thumbnail, :embed_url, :source, :sort_order, :is_active)");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ ग्यालरी आइटम थपियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM gallery_items WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">मेटाइयो</div>';
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE gallery_items SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM gallery_items WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editItem = $stmt->fetch();
}

$where = $filter === 'all' ? '' : "WHERE type = :type";
$params = $filter === 'all' ? [] : [':type' => $filter];
$stmt = $db->prepare("SELECT * FROM gallery_items $where ORDER BY sort_order ASC, created_at DESC");
$stmt->execute($params);
$gallery = $stmt->fetchAll();

function galleryThumb($item) {
    if (!empty($item['thumbnail'])) return $item['thumbnail'];
    if (!empty($item['url']) && $item['type'] === 'image') return $item['url'];
    if (!empty($item['embed_url'])) return 'https://img.youtube.com/vi/' . (preg_match('/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $item['embed_url'], $m) ? $m[1] : '') . '/hqdefault.jpg';
    return '';
}
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>ग्यालरी</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editItem ? 'सम्पादन गर्नुहोस्' : 'नयाँ ग्यालरी आइटम' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field">
                    <label>प्रकार *</label>
                    <select name="type" class="form-input">
                        <option value="image" <?= $editItem && $editItem['type'] === 'image' ? 'selected' : '' ?>>छवि</option>
                        <option value="video" <?= $editItem && $editItem['type'] === 'video' ? 'selected' : '' ?>>भिडियो</option>
                        <option value="audio" <?= $editItem && $editItem['type'] === 'audio' ? 'selected' : '' ?>>अडियो</option>
                    </select>
                </div>
                <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editItem['title_ne'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editItem['title_en'] ?? '') ?>"></div>
                <div class="field">
                    <label>छवि / फाइल URL</label>
                    <div class="file-upload-wrap" data-field="url">
                        <div class="file-zone" data-field="url" role="button" tabindex="0" aria-label="Upload file">
                            <?php if (!empty($editItem['url'])): ?>
                            <img src="<?= htmlspecialchars($editItem['url']) ?>" alt="preview" class="file-preview">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($editItem['url'])): ?>
                        <button type="button" class="file-clear" data-field="url" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="url" value="<?= htmlspecialchars($editItem['url'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="field"><label>थम्बनेल URL</label><input name="thumbnail" class="form-input" placeholder="यदि भिडियो हो भने" value="<?= htmlspecialchars($editItem['thumbnail'] ?? '') ?>"></div>
                <div class="field"><label>Embed URL</label><input name="embed_url" class="form-input" placeholder="https://www.youtube.com/embed/..." value="<?= htmlspecialchars($editItem['embed_url'] ?? '') ?>"></div>
                <div class="field"><label>स्रोत</label><input name="source" class="form-input" placeholder="YouTube / Facebook" value="<?= htmlspecialchars($editItem['source'] ?? '') ?>"></div>
                <div class="field"><label>क्रम</label><input name="sort_order" type="number" class="form-input" value="<?= (int)($editItem['sort_order'] ?? 0) ?>"></div>
                <div class="field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= $editItem === null || ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>>
                        सक्रिय
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_gallery" class="btn btn-primary"><?= $editItem ? 'अपडेट गर्नुहोस्' : 'सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editItem): ?>
                <a href="gallery.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div>
        <div class="tab-filter-bar">
            <a href="gallery.php" class="tab-filter <?= $filter === 'all' ? 'active' : '' ?>">सबै</a>
            <a href="gallery.php?type=image" class="tab-filter <?= $filter === 'image' ? 'active' : '' ?>">छवि</a>
            <a href="gallery.php?type=video" class="tab-filter <?= $filter === 'video' ? 'active' : '' ?>">भिडियो</a>
            <a href="gallery.php?type=audio" class="tab-filter <?= $filter === 'audio' ? 'active' : '' ?>">अडियो</a>
        </div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>थम्बनेल</th><th>शीर्षक</th><th>प्रकार</th><th>स्रोत</th><th>क्रम</th><th>स्थिति</th><th>कार्य</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($gallery as $g): ?>
                    <tr>
                        <td>
                            <?php $thumb = galleryThumb($g); if ($thumb): ?>
                            <img src="<?= htmlspecialchars($thumb) ?>" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:4px">
                            <?php else: ?>
                            <span style="color:var(--muted);font-size:11px">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="td-name"><?= htmlspecialchars($g['title_ne']) ?><?= $g['title_en'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($g['title_en']) . '</small>' : '' ?></td>
                        <td><span class="badge badge-<?= $g['type'] === 'image' ? 'info' : ($g['type'] === 'video' ? 'gold' : 'muted') ?>"><?= $g['type'] ?></span></td>
                        <td><?= htmlspecialchars($g['source'] ?? '-') ?></td>
                        <td><?= (int)$g['sort_order'] ?></td>
                        <td><span class="badge badge-<?= $g['is_active'] ? 'confirmed' : 'muted' ?>"><?= $g['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
                        <td>
                            <div class="action-form">
                                <a href="?edit=<?= $g['id'] ?>" class="btn-sm">सम्पादन</a>
                                <a href="?toggle=<?= $g['id'] ?>&<?= csrfQuery() ?>" class="btn-sm"><?= $g['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
                                <a href="?delete=<?= $g['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($gallery)): ?>
                    <tr><td colspan="7" class="empty-state">ग्यालरी खाली छ</td></tr>
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
        fd.append('type', 'gallery');

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
