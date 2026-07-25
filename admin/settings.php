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

<div class="two-col-layout">
    <div class="form-card">
        <h3>साइट सेटिङ्स</h3>
        <form method="POST">
            <?= csrfField() ?>
            <div class="form-grid">
                <div class="form-field"><label class="form-label">साइट नाम</label><input name="site_name" class="form-input" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">ट्यागलाइन (नेपाली)</label><input name="site_tagline_ne" class="form-input" value="<?= htmlspecialchars($settings['site_tagline_ne'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">ट्यागलाइन (अङ्ग्रेजी)</label><input name="site_tagline_en" class="form-input" value="<?= htmlspecialchars($settings['site_tagline_en'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">फोन</label><input name="phone" class="form-input" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">फोन (प्रदर्शन)</label><input name="phone_display" class="form-input" value="<?= htmlspecialchars($settings['phone_display'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">इमेल</label><input name="email" type="email" class="form-input" value="<?= htmlspecialchars($settings['email'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">ठेगाना</label><input name="address" class="form-input" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">YouTube URL</label><input name="youtube_url" class="form-input" placeholder="https://youtube.com/..." value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">Facebook URL</label><input name="facebook_url" class="form-input" placeholder="https://facebook.com/..." value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">WhatsApp नम्बर</label><input name="whatsapp_number" class="form-input" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>"></div>
                <div class="form-field">
                    <label class="form-label">लोगो URL</label>
                    <div class="file-upload-wrap" data-field="logo_url">
                        <div class="file-zone" data-field="logo_url" data-upload-type="general" role="button" tabindex="0" aria-label="Upload logo">
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
                <div class="form-field">
                    <label class="form-label">फेभिकन URL</label>
                    <div class="file-upload-wrap" data-field="favicon_url">
                        <div class="file-zone" data-field="favicon_url" data-upload-type="general" role="button" tabindex="0" aria-label="Upload favicon">
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
                <div class="form-field"><label class="form-label">खुल्ने समय</label><input name="business_hours" class="form-input" value="<?= htmlspecialchars($settings['business_hours'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">परामर्श समय</label><input name="consultation_hours" class="form-input" value="<?= htmlspecialchars($settings['consultation_hours'] ?? '') ?>"></div>
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
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label">हालको पासवर्ड</label>
                    <input type="password" name="current_password" required class="form-input">
                </div>
                <div class="form-field">
                    <label class="form-label">नयाँ पासवर्ड</label>
                    <input type="password" name="new_password" required minlength="6" class="form-input">
                </div>
                <div class="form-field">
                    <label class="form-label">नयाँ पासवर्ड पुष्टि गर्नुहोस्</label>
                    <input type="password" name="confirm_password" required minlength="6" class="form-input">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="change_password" class="btn btn-primary">परिवर्तन गर्नुहोस्</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
