<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($_POST['current_password'] ?? '', $admin['password_hash'])) {
        $alertHtml = '<div class="alert-error">हालको पासवर्ड मिलेन</div>';
    } elseif (strlen($_POST['new_password'] ?? '') < 6) {
        $alertHtml = '<div class="alert-error">नयाँ पासवर्ड कम्तिमा ६ क्यारेक्टर हुनुपर्छ</div>';
    } elseif ($_POST['new_password'] !== ($_POST['confirm_password'] ?? '')) {
        $alertHtml = '<div class="alert-error">नयाँ पासवर्ड र पुष्टि पासवर्ड मिलेन</div>';
    } else {
        $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $db->prepare("UPDATE admin_users SET password_hash = :hash WHERE id = :id")->execute([':hash' => $newHash, ':id' => $_SESSION['admin_id']]);
        $alertHtml = '<div class="alert-success">पासवर्ड सफलतापूर्वक परिवर्तन गरियो</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $fields = ['site_name','site_tagline_ne','site_tagline_en','phone','phone_display','email','address','youtube_url','facebook_url','whatsapp_number','logo_url','favicon_url','business_hours','consultation_hours'];
    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_value = :val2");
    foreach ($fields as $f) {
        $v = sanitize($_POST[$f] ?? '');
        $stmt->execute([':key' => $f, ':val' => $v, ':val2' => $v]);
    }
    $alertHtml = '<div class="alert-success">सेटिङ्स सुरक्षित गरियो</div>';
}

$settings = [];
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) $settings[$row['setting_key']] = $row['setting_value'];
?>
<?= $alertHtml ?>

<div class="page-header">
    <h1>सेटिङ्स</h1>
</div>

<div class="settings-grid">
    <div class="form-card">
        <h3>साइट सेटिङ्स</h3>
        <form method="POST">
            <?= csrfField() ?>
            <div class="form-grid">
                <div class="field"><label>साइट नाम</label><input name="site_name" class="form-input" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
                <div class="field"><label>ट्यागलाइन (नेपाली)</label><input name="site_tagline_ne" class="form-input" value="<?= htmlspecialchars($settings['site_tagline_ne'] ?? '') ?>"></div>
                <div class="field"><label>ट्यागलाइन (अङ्ग्रेजी)</label><input name="site_tagline_en" class="form-input" value="<?= htmlspecialchars($settings['site_tagline_en'] ?? '') ?>"></div>
                <div class="field"><label>फोन</label><input name="phone" class="form-input" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>"></div>
                <div class="field"><label>फोन (प्रदर्शन)</label><input name="phone_display" class="form-input" value="<?= htmlspecialchars($settings['phone_display'] ?? '') ?>"></div>
                <div class="field"><label>इमेल</label><input name="email" type="email" class="form-input" value="<?= htmlspecialchars($settings['email'] ?? '') ?>"></div>
                <div class="field"><label>ठेगाना</label><input name="address" class="form-input" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"></div>
                <div class="field"><label>YouTube URL</label><input name="youtube_url" class="form-input" placeholder="https://youtube.com/..." value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>"></div>
                <div class="field"><label>Facebook URL</label><input name="facebook_url" class="form-input" placeholder="https://facebook.com/..." value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>"></div>
                <div class="field"><label>WhatsApp नम्बर</label><input name="whatsapp_number" class="form-input" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>"></div>
                <div class="field">
                    <label>लोगो URL</label>
                    <div class="file-upload-wrap" data-field="logo_url">
                        <div class="file-zone" data-field="logo_url" role="button" tabindex="0" aria-label="Upload logo">
                            <?php if (!empty($settings['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($settings['logo_url']) ?>" alt="preview" class="file-preview">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop logo here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($settings['logo_url'])): ?>
                        <button type="button" class="file-clear" data-field="logo_url" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="logo_url" value="<?= htmlspecialchars($settings['logo_url'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="field">
                    <label>फेभिकन URL</label>
                    <div class="file-upload-wrap" data-field="favicon_url">
                        <div class="file-zone" data-field="favicon_url" role="button" tabindex="0" aria-label="Upload favicon">
                            <?php if (!empty($settings['favicon_url'])): ?>
                            <img src="<?= htmlspecialchars($settings['favicon_url']) ?>" alt="preview" class="file-preview" style="max-height:40px;max-width:40px">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop favicon here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($settings['favicon_url'])): ?>
                        <button type="button" class="file-clear" data-field="favicon_url" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="favicon_url" value="<?= htmlspecialchars($settings['favicon_url'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="field"><label>खुल्ने समय</label><input name="business_hours" class="form-input" value="<?= htmlspecialchars($settings['business_hours'] ?? '') ?>"></div>
                <div class="field"><label>परामर्श समय</label><input name="consultation_hours" class="form-input" value="<?= htmlspecialchars($settings['consultation_hours'] ?? '') ?>"></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_settings" class="btn btn-primary">सेटिङ्स सुरक्षित गर्नुहोस्</button>
            </div>
        </form>
    </div>

    <div class="form-card">
        <h3>पासवर्ड परिवर्तन गर्नुहोस्</h3>
        <form method="POST">
            <?= csrfField() ?>
            <div class="field">
                <label class="form-label">हालको पासवर्ड</label>
                <input type="password" name="current_password" required class="form-input">
            </div>
            <div class="field">
                <label class="form-label">नयाँ पासवर्ड</label>
                <input type="password" name="new_password" required minlength="6" class="form-input">
            </div>
            <div class="field">
                <label class="form-label">नयाँ पासवर्ड पुष्टि गर्नुहोस्</label>
                <input type="password" name="confirm_password" required minlength="6" class="form-input">
            </div>
            <div class="form-actions">
                <button type="submit" name="change_password" class="btn btn-primary">परिवर्तन गर्नुहोस्</button>
            </div>
        </form>
    </div>
</div>

<style>
.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 820px) {
    .settings-grid { grid-template-columns: 1fr; }
}
</style>

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
        fd.append('type', 'general');

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
