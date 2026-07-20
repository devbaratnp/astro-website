<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

function generateSlug($str) {
    $str = mb_strtolower(trim($str));
    $str = preg_replace('/[^\w\s\-]/u', '', $str);
    $str = preg_replace('/\s+/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-') ?: 'post-' . time();
}

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
        ':is_published' => !empty($_POST['is_published']) ? 1 : 0,
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE articles SET title_ne=:title_ne, title_en=:title_en, slug=:slug, content_ne=:content_ne, content_en=:content_en, excerpt_ne=:excerpt_ne, excerpt_en=:excerpt_en, cover_image=:cover_image, is_published=:is_published WHERE id=:id");
        $stmt->execute($data);
        echo '<div class="alert alert-success">लेख अपडेट गरियो</div>';
    } else {
        // Ensure unique slug
        $check = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug = :slug");
        $check->execute([':slug' => $slug]);
        if ($check->fetchColumn() > 0) {
            $slug .= '-' . time();
            $data[':slug'] = $slug;
        }
        $stmt = $db->prepare("INSERT INTO articles (title_ne, title_en, slug, content_ne, content_en, excerpt_ne, excerpt_en, cover_image, is_published, published_at) VALUES (:title_ne, :title_en, :slug, :content_ne, :content_en, :excerpt_ne, :excerpt_en, :cover_image, :is_published, CASE WHEN :is_published = 1 THEN NOW() ELSE NULL END)");
        $stmt->execute($data);
        echo '<div class="alert alert-success">नयाँ लेख प्रकाशित गरियो</div>';
    }
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    echo '<div class="alert alert-success">लेख मेटाइयो</div>';
}

$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editArticle = $stmt->fetch();
}

$articles = $db->query("SELECT id, title_ne, title_en, slug, is_published, excerpt_ne, published_at, created_at FROM articles ORDER BY created_at DESC")->fetchAll();
?>
<h1>लेख व्यवस्थापन</h1>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
  <div class="form-card">
    <h3><?= $editArticle ? 'लेख सम्पादन गर्नुहोस्' : 'नयाँ लेख लेख्नुहोस्' ?></h3>
    <form method="POST">
      <?= csrfField() ?>
      <?php if ($editArticle): ?>
        <input type="hidden" name="id" value="<?= $editArticle['id'] ?>">
      <?php endif; ?>
      <div class="form-grid">
        <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required value="<?= htmlspecialchars($editArticle['title_ne'] ?? '') ?>"></div>
        <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" value="<?= htmlspecialchars($editArticle['title_en'] ?? '') ?>"></div>
        <div class="field"><label>URL Slug</label><input name="slug" placeholder="auto-generated" value="<?= htmlspecialchars($editArticle['slug'] ?? '') ?>"></div>
        <div class="field"><label>कभर छवि URL</label><input name="cover_image" placeholder="/assets/blog/..." value="<?= htmlspecialchars($editArticle['cover_image'] ?? '') ?>"></div>
        <div class="field full"><label>सारांश (नेपाली)</label><textarea name="excerpt_ne" rows="2"><?= htmlspecialchars($editArticle['excerpt_ne'] ?? '') ?></textarea></div>
        <div class="field full"><label>सारांश (अङ्ग्रेजी)</label><textarea name="excerpt_en" rows="2"><?= htmlspecialchars($editArticle['excerpt_en'] ?? '') ?></textarea></div>
        <div class="field full"><label>सामग्री (नेपाली) *</label><textarea name="content_ne" required rows="8" style="font-family:monospace"><?= htmlspecialchars($editArticle['content_ne'] ?? '') ?></textarea></div>
        <div class="field full"><label>सामग्री (अङ्ग्रेजी)</label><textarea name="content_en" rows="8" style="font-family:monospace"><?= htmlspecialchars($editArticle['content_en'] ?? '') ?></textarea></div>
        <div class="field" style="display:flex;align-items:center;gap:12px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="is_published" value="1" <?= !empty($editArticle['is_published']) ? 'checked' : '' ?>>
            प्रकाशित गर्नुहोस्
          </label>
        </div>
      </div>
      <div style="display:flex;gap:12px;margin-top:16px">
        <button type="submit" name="save_article" class="btn btn-primary"><?= $editArticle ? 'अपडेट गर्नुहोस्' : 'लेख सुरक्षित गर्नुहोस्' ?></button>
        <?php if ($editArticle): ?>
          <a href="articles.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="admin-table-wrapper">
    <table class="admin-table">
      <thead>
        <tr><th>शीर्षक</th><th>Slug</th><th>स्थिति</th><th>मिति</th><th>कार्य</th></tr>
      </thead>
      <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
          <td><strong><?= htmlspecialchars($a['title_ne']) ?></strong><?= $a['title_en'] ? '<br><small style="color:var(--muted)">' . htmlspecialchars($a['title_en']) . '</small>' : '' ?></td>
          <td><code style="font-size:.8rem"><?= htmlspecialchars($a['slug']) ?></code></td>
          <td><span class="badge badge-<?= $a['is_published'] ? 'confirmed' : 'pending' ?>"><?= $a['is_published'] ? 'प्रकाशित' : 'मस्यौदा' ?></span></td>
          <td style="font-size:.85rem;color:var(--muted)"><?= $a['published_at'] ?? $a['created_at'] ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <?php if ($a['is_published'] && $a['slug']): ?>
              <a href="<?= BASE_URL ?>/article/<?= urlencode($a['slug']) ?>" target="_blank" class="btn-small btn-gold">हेर्नुहोस्</a>
              <?php endif; ?>
              <a href="?edit=<?= $a['id'] ?>" class="btn-small">सम्पादन</a>
              <a href="?delete=<?= $a['id'] ?>&<?= csrfQuery() ?>" class="btn-small btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($articles)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted)">कुनै लेख छैन</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
