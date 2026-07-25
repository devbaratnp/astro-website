<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_article'])) {
    $id = $_POST['id'] ?? null;
    $slug = $_POST['slug'] ?: generateSlug($_POST['title_ne']);

    $data = [
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':slug' => $slug,
        ':content_ne' => $_POST['content_ne'],
        ':content_en' => sanitize($_POST['content_en'] ?? ''),
        ':excerpt_ne' => sanitize($_POST['excerpt_ne'] ?? ''),
        ':excerpt_en' => sanitize($_POST['excerpt_en'] ?? ''),
        ':cover_image' => sanitize($_POST['cover_image'] ?? ''),
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE articles SET title_ne=:title_ne, title_en=:title_en, slug=:slug, content_ne=:content_ne, content_en=:content_en, excerpt_ne=:excerpt_ne, excerpt_en=:excerpt_en, cover_image=:cover_image, is_published=1, published_at=COALESCE(published_at, NOW()) WHERE id=:id");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">लेख अपडेट गरियो</div>';
    } else {
        $check = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug = :slug");
        $check->execute([':slug' => $slug]);
        if ($check->fetchColumn() > 0) {
            $slug .= '-' . time();
            $data[':slug'] = $slug;
        }
        $stmt = $db->prepare("INSERT INTO articles (title_ne, title_en, slug, content_ne, content_en, excerpt_ne, excerpt_en, cover_image, is_published, published_at) VALUES (:title_ne, :title_en, :slug, :content_ne, :content_en, :excerpt_ne, :excerpt_en, :cover_image, 1, NOW())");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ लेख प्रकाशित गरियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">लेख मेटाइयो</div>';
}

$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editArticle = $stmt->fetch();
}

$articles = $db->query("SELECT id, title_ne, title_en, slug, is_published, excerpt_ne, published_at, created_at FROM articles ORDER BY created_at DESC")->fetchAll();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>लेख व्यवस्थापन</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editArticle ? 'लेख सम्पादन गर्नुहोस्' : 'नयाँ लेख लेख्नुहोस्' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editArticle): ?>
            <input type="hidden" name="id" value="<?= $editArticle['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-field"><label class="form-label">शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editArticle['title_ne'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editArticle['title_en'] ?? '') ?>"></div>
                <div class="form-field"><label class="form-label">URL Slug</label><input name="slug" class="form-input" placeholder="auto-generated" value="<?= htmlspecialchars($editArticle['slug'] ?? '') ?>"></div>
                <div class="form-field">
                    <label class="form-label">कभर छवि</label>
                    <div class="file-upload-wrap" data-field="cover_image">
                        <div class="file-zone" data-field="cover_image" data-upload-type="article" role="button" tabindex="0" aria-label="Upload cover image">
                            <?php if (!empty($editArticle['cover_image'])): ?>
                            <img src="<?= htmlspecialchars($editArticle['cover_image']) ?>" alt="preview" class="file-preview">
                            <?php else: ?>
                            <span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($editArticle['cover_image'])): ?>
                        <button type="button" class="file-clear" data-field="cover_image" aria-label="Clear image">✕</button>
                        <?php endif; ?>
                        <input type="hidden" name="cover_image" value="<?= htmlspecialchars($editArticle['cover_image'] ?? '') ?>" class="file-hidden">
                    </div>
                </div>
                <div class="form-field full"><label class="form-label">सारांश (नेपाली)</label><textarea name="excerpt_ne" rows="2" class="form-textarea"><?= htmlspecialchars($editArticle['excerpt_ne'] ?? '') ?></textarea></div>
                <div class="form-field full"><label class="form-label">सारांश (अङ्ग्रेजी)</label><textarea name="excerpt_en" rows="2" class="form-textarea"><?= htmlspecialchars($editArticle['excerpt_en'] ?? '') ?></textarea></div>
                <div class="form-field full"><label class="form-label">सामग्री (नेपाली) *</label><textarea name="content_ne" required rows="8" class="form-textarea" style="font-family:monospace"><?= htmlspecialchars($editArticle['content_ne'] ?? '') ?></textarea></div>
                <div class="form-field full"><label class="form-label">सामग्री (अङ्ग्रेजी)</label><textarea name="content_en" rows="8" class="form-textarea" style="font-family:monospace"><?= htmlspecialchars($editArticle['content_en'] ?? '') ?></textarea></div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_article" class="btn btn-primary"><?= $editArticle ? 'अपडेट गर्नुहोस्' : 'लेख सुरक्षित गर्नुहोस्' ?></button>
                <?php if ($editArticle): ?>
                <a href="articles.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrap">
        <table class="data-table" role="table" aria-label="Articles list">
            <thead>
                <tr><th>शीर्षक</th><th>Slug</th><th>स्थिति</th><th>मिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $a): ?>
                <tr>
                    <td class="td-name"><?= htmlspecialchars($a['title_ne']) ?><?= $a['title_en'] ? '<br><small class="text-muted">' . htmlspecialchars($a['title_en']) . '</small>' : '' ?></td>
                    <td><code style="font-size:.8rem"><?= htmlspecialchars($a['slug']) ?></code></td>
                    <td><span class="badge badge-confirmed">प्रकाशित</span></td>
                    <td><?= $a['published_at'] ?? $a['created_at'] ?></td>
                    <td>
                        <div class="action-form">
                            <?php if ($a['slug']): ?>
                            <a href="<?= BASE_URL ?>/article/<?= urlencode($a['slug']) ?>" target="_blank" rel="noreferrer" class="btn btn-xs btn-gold">हेर्नुहोस्</a>
                            <?php endif; ?>
                            <a href="?edit=<?= $a['id'] ?>" class="btn btn-xs btn-outline">सम्पादन</a>
                            <a href="?delete=<?= $a['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-danger" data-confirm="पक्का मेटाउने?">मेटाउनुहोस्</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($articles)): ?>
                <tr><td colspan="5"><div class="empty-state">कुनै लेख छैन</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mobile-records">
    <?php if (empty($articles)): ?>
    <div class="empty-state">कुनै लेख छैन</div>
    <?php else: foreach ($articles as $a): ?>
    <div class="mobile-record">
        <div class="mobile-record-header">
            <h3 class="mobile-record-title"><?= htmlspecialchars($a['title_ne']) ?></h3>
            <span class="badge badge-confirmed">प्रकाशित</span>
        </div>
        <div class="mobile-record-body">
            <div class="mobile-record-field">
                <span>Slug</span>
                <strong><?= htmlspecialchars($a['slug']) ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>मिति</span>
                <strong><?= $a['published_at'] ?? $a['created_at'] ?></strong>
            </div>
        </div>
        <div class="mobile-record-footer">
            <?php if ($a['slug']): ?>
            <a href="<?= BASE_URL ?>/article/<?= urlencode($a['slug']) ?>" target="_blank" rel="noreferrer" class="btn btn-xs btn-gold">हेर्नुहोस्</a>
            <?php endif; ?>
            <a href="?edit=<?= $a['id'] ?>" class="btn btn-xs btn-outline">सम्पादन</a>
            <a href="?delete=<?= $a['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-danger" data-confirm="पक्का मेटाउने?">मेटाउनुहोस्</a>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
