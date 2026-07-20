<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $id = $_POST['id'] ?? null;
    $images = [];
    if (!empty($_POST['image_urls'])) {
        $urls = explode("\n", trim($_POST['image_urls']));
        foreach ($urls as $u) {
            $u = trim($u);
            if ($u) $images[] = $u;
        }
    }

    $data = [
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
        ':description_en' => sanitize($_POST['description_en'] ?? ''),
        ':price' => $_POST['price'] ?: 0,
        ':compare_price' => $_POST['compare_price'] ?: null,
        ':images' => json_encode($images, JSON_UNESCAPED_UNICODE),
        ':category' => sanitize($_POST['category'] ?? ''),
        ':stock_status' => $_POST['stock_status'] ?? 'in_stock',
    ];

    if ($id) {
        $data[':id'] = $id;
        $stmt = $db->prepare("UPDATE products SET title_ne=:title_ne, title_en=:title_en, description_ne=:description_ne, description_en=:description_en, price=:price, compare_price=:compare_price, images=:images, category=:category, stock_status=:stock_status WHERE id=:id");
        $stmt->execute($data);
        echo '<div class="alert alert-success">उत्पादन अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO products (title_ne, title_en, description_ne, description_en, price, compare_price, images, category, stock_status) VALUES (:title_ne, :title_en, :description_ne, :description_en, :price, :compare_price, :images, :category, :stock_status)");
        $stmt->execute($data);
        echo '<div class="alert alert-success">नयाँ उत्पादन थपियो</div>';
    }
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE products SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    echo '<div class="alert alert-success">स्थिति परिवर्तन गरियो</div>';
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    echo '<div class="alert alert-success">उत्पादन मेटाइयो</div>';
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$products = $db->query("SELECT * FROM products ORDER BY category, title_ne")->fetchAll();
?>

<div class="page-header">
    <h1>उत्पादन व्यवस्थापन</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
  <div class="form-card">
    <h3><?= $editProduct ? 'उत्पादन सम्पादन गर्नुहोस्' : 'नयाँ उत्पादन थप्नुहोस्' ?></h3>
    <form method="POST">
      <?= csrfField() ?>
      <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
      <?php endif; ?>
      <div class="form-grid">
        <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required value="<?= htmlspecialchars($editProduct['title_ne'] ?? '') ?>"></div>
        <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" value="<?= htmlspecialchars($editProduct['title_en'] ?? '') ?>"></div>
        <div class="field"><label>मूल्य (रु) *</label><input name="price" type="number" step="0.01" required value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>"></div>
        <div class="field"><label>साधारण मूल्य (रु)</label><input name="compare_price" type="number" step="0.01" value="<?= htmlspecialchars($editProduct['compare_price'] ?? '') ?>"></div>
        <div class="field"><label>कोटि</label><input name="category" placeholder="जस्तै: माला, धूप, मूर्ति" value="<?= htmlspecialchars($editProduct['category'] ?? '') ?>"></div>
        <div class="field"><label>स्टक स्थिति</label>
          <select name="stock_status">
            <option value="in_stock" <?= ($editProduct['stock_status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>स्टकमा छ</option>
            <option value="out_of_stock" <?= ($editProduct['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>स्टक छैन</option>
            <option value="pre_order" <?= ($editProduct['stock_status'] ?? '') === 'pre_order' ? 'selected' : '' ?>>प्रि-अर्डर</option>
          </select>
        </div>
        <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="3"><?= htmlspecialchars($editProduct['description_ne'] ?? '') ?></textarea></div>
        <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="3"><?= htmlspecialchars($editProduct['description_en'] ?? '') ?></textarea></div>
        <div class="field full"><label>छवि URL हरू (प्रति लाइन एक)</label>
          <textarea name="image_urls" rows="3" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?php
            $imgs = $editProduct ? json_decode($editProduct['images'] ?? '[]', true) : [];
            echo htmlspecialchars(implode("\n", $imgs));
          ?></textarea>
        </div>
      </div>
      <div style="display:flex;gap:12px;margin-top:16px">
        <button type="submit" name="save_product" class="btn btn-primary"><?= $editProduct ? 'अपडेट गर्नुहोस्' : 'उत्पादन थप्नुहोस्' ?></button>
        <?php if ($editProduct): ?>
          <a href="products.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="admin-table-wrapper">
    <table class="admin-table">
      <thead>
        <tr><th>उत्पादन</th><th>मूल्य</th><th>कोटि</th><th>स्टक</th><th>स्थिति</th><th>कार्य</th></tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($p['title_ne']) ?></strong>
            <?php if ($p['title_en']): ?><br><small style="color:var(--muted)"><?= htmlspecialchars($p['title_en']) ?></small><?php endif; ?>
          </td>
          <td style="font-weight:700;color:var(--wine)">रु <?= number_format($p['price']) ?></td>
          <td><?= htmlspecialchars($p['category'] ?: '—') ?></td>
          <td>
            <span class="badge badge-<?= $p['stock_status'] === 'in_stock' ? 'confirmed' : ($p['stock_status'] === 'pre_order' ? 'pending' : 'cancelled') ?>">
              <?= $p['stock_status'] === 'in_stock' ? 'स्टकमा' : ($p['stock_status'] === 'pre_order' ? 'प्रि-अर्डर' : 'सकिएको') ?>
            </span>
          </td>
          <td><span class="badge badge-<?= $p['is_active'] ? 'confirmed' : 'cancelled' ?>"><?= $p['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="?edit=<?= $p['id'] ?>" class="btn-small">सम्पादन</a>
              <a href="?toggle=<?= $p['id'] ?>&<?= csrfQuery() ?>" class="btn-small"><?= $p['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
              <a href="?delete=<?= $p['id'] ?>&<?= csrfQuery() ?>" class="btn-small btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted)">कुनै उत्पादन छैन — माथि नयाँ थप्नुहोस्</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
