<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $id = $_POST['id'] ?? null;
    $images = [];
    if (!empty($_POST['image_urls'])) {
        $decoded = json_decode($_POST['image_urls'], true);
        if (is_array($decoded)) {
            $images = $decoded;
        } else {
            $urls = explode("\n", trim($_POST['image_urls']));
            foreach ($urls as $u) {
                $u = trim($u);
                if ($u) $images[] = $u;
            }
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
        $alertHtml = '<div class="alert-success">उत्पादन अपडेट गरियो</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO products (title_ne, title_en, description_ne, description_en, price, compare_price, images, category, stock_status) VALUES (:title_ne, :title_en, :description_ne, :description_en, :price, :compare_price, :images, :category, :stock_status)");
        $stmt->execute($data);
        $alertHtml = '<div class="alert-success">नयाँ उत्पादन थपियो</div>';
    }
}

if (isset($_GET['toggle'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE products SET is_active = NOT is_active WHERE id = :id");
    $stmt->execute([':id' => $_GET['toggle']]);
    $alertHtml = '<div class="alert-success">स्थिति परिवर्तन गरियो</div>';
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">उत्पादन मेटाइयो</div>';
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$products = $db->query("SELECT * FROM products ORDER BY category, title_ne")->fetchAll();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>उत्पादन व्यवस्थापन</h1>
</div>

<div class="two-col-layout">
    <div class="form-card">
        <h3><?= $editProduct ? 'उत्पादन सम्पादन गर्नुहोस्' : 'नयाँ उत्पादन थप्नुहोस्' ?></h3>
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required class="form-input" value="<?= htmlspecialchars($editProduct['title_ne'] ?? '') ?>"></div>
                <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" class="form-input" value="<?= htmlspecialchars($editProduct['title_en'] ?? '') ?>"></div>
                <div class="field"><label>मूल्य (रु) *</label><input name="price" type="number" step="0.01" required class="form-input" value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>"></div>
                <div class="field"><label>साधारण मूल्य (रु)</label><input name="compare_price" type="number" step="0.01" class="form-input" value="<?= htmlspecialchars($editProduct['compare_price'] ?? '') ?>"></div>
                <div class="field"><label>कोटि</label><input name="category" class="form-input" placeholder="जस्तै: माला, धूप, मूर्ति" value="<?= htmlspecialchars($editProduct['category'] ?? '') ?>"></div>
                <div class="field"><label>स्टक स्थिति</label>
                    <select name="stock_status" class="form-input">
                        <option value="in_stock" <?= ($editProduct['stock_status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>स्टकमा छ</option>
                        <option value="out_of_stock" <?= ($editProduct['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>स्टक छैन</option>
                        <option value="pre_order" <?= ($editProduct['stock_status'] ?? '') === 'pre_order' ? 'selected' : '' ?>>प्रि-अर्डर</option>
                    </select>
                </div>
                <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="3" class="form-input"><?= htmlspecialchars($editProduct['description_ne'] ?? '') ?></textarea></div>
                <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="3" class="form-input"><?= htmlspecialchars($editProduct['description_en'] ?? '') ?></textarea></div>
                <div class="field full">
                    <label>उत्पादन छविहरू</label>
                    <div class="file-upload-wrap" data-field="product_images">
                        <div class="file-zone" data-field="product_images" role="button" tabindex="0" aria-label="Upload product image">
                            <span><span class="upload-icon">☁️</span>Tap to add product image</span>
                        </div>
                        <input type="hidden" name="image_urls" value='<?= htmlspecialchars($editProduct ? ($editProduct['images'] ?? '[]') : '[]') ?>' class="file-hidden" id="product-images-data">
                        <div class="multi-preview" id="product-images-preview">
                            <?php
                                $imgs = $editProduct ? json_decode($editProduct['images'] ?? '[]', true) : [];
                                foreach ($imgs as $url):
                            ?>
                            <div class="multi-preview-item">
                                <img src="<?= htmlspecialchars($url) ?>" alt="">
                                <button type="button" class="multi-preview-remove" data-url="<?= htmlspecialchars($url) ?>" aria-label="Remove image">✕</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_product" class="btn btn-primary"><?= $editProduct ? 'अपडेट गर्नुहोस्' : 'उत्पादन थप्नुहोस्' ?></button>
                <?php if ($editProduct): ?>
                <a href="products.php" class="btn btn-outline">रद्द गर्नुहोस्</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>उत्पादन</th><th>मूल्य</th><th>कोटि</th><th>स्टक</th><th>स्थिति</th><th>कार्य</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td class="td-name">
                        <?= htmlspecialchars($p['title_ne']) ?>
                        <?php if ($p['title_en']): ?><br><small style="color:var(--muted)"><?= htmlspecialchars($p['title_en']) ?></small><?php endif; ?>
                    </td>
                    <td class="td-amount">रु <?= number_format($p['price']) ?></td>
                    <td><?= htmlspecialchars($p['category'] ?: '—') ?></td>
                    <td>
                        <span class="badge badge-<?= $p['stock_status'] === 'in_stock' ? 'confirmed' : ($p['stock_status'] === 'pre_order' ? 'pending' : 'cancelled') ?>">
                            <?= $p['stock_status'] === 'in_stock' ? 'स्टकमा' : ($p['stock_status'] === 'pre_order' ? 'प्रि-अर्डर' : 'सकिएको') ?>
                        </span>
                    </td>
                    <td><span class="badge badge-<?= $p['is_active'] ? 'confirmed' : 'cancelled' ?>"><?= $p['is_active'] ? 'सक्रिय' : 'निष्क्रिय' ?></span></td>
                    <td>
                        <div class="action-form">
                            <a href="?edit=<?= $p['id'] ?>" class="btn-sm">सम्पादन</a>
                            <a href="?toggle=<?= $p['id'] ?>&<?= csrfQuery() ?>" class="btn-sm"><?= $p['is_active'] ? 'निष्क्रिय' : 'सक्रिय' ?></a>
                            <a href="?delete=<?= $p['id'] ?>&<?= csrfQuery() ?>" class="btn-sm btn-danger" onclick="return confirm('पक्का मेटाउने?')">मेटाउनुहोस्</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                <tr><td colspan="6" class="empty-state">कुनै उत्पादन छैन — माथि नयाँ थप्नुहोस्</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.multi-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}
.multi-preview-item {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--line-light);
    flex-shrink: 0;
}
.multi-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.multi-preview-remove {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: 0;
    background: var(--wine, #b33a3a);
    color: #fff;
    font-size: 13px;
    cursor: pointer;
    display: grid;
    place-items: center;
    padding: 0;
    box-shadow: 0 1px 4px rgba(0,0,0,.3);
    min-width: 44px;
    min-height: 44px;
    background-clip: padding-box;
}
.multi-preview-remove:active {
    transform: scale(.9);
}
</style>

<script>
(function(){
    var API_BASE = '<?= BASE_URL ?>/backend/api';

    function getImageUrls() {
        var el = document.getElementById('product-images-data');
        try { return JSON.parse(el.value || '[]'); } catch(e) { return []; }
    }

    function setImageUrls(urls) {
        document.getElementById('product-images-data').value = JSON.stringify(urls);
        renderPreview(urls);
    }

    function renderPreview(urls) {
        var container = document.getElementById('product-images-preview');
        container.innerHTML = urls.map(function(url) {
            return '<div class="multi-preview-item">' +
                '<img src="' + url.replace(/'/g, "\\'") + '" alt="">' +
                '<button type="button" class="multi-preview-remove" data-url="' + url.replace(/'/g, "\\'") + '" aria-label="Remove image">✕</button>' +
            '</div>';
        }).join('');
    }

    document.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.multi-preview-remove');
        if (removeBtn) {
            var url = removeBtn.dataset.url;
            var urls = getImageUrls().filter(function(u) { return u !== url; });
            setImageUrls(urls);
        }
    });

    document.addEventListener('click', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        var input = zone.querySelector('input[type="file"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp,image/gif';
            input.style.display = 'none';
            zone.appendChild(input);
        }
        input.click();
    });

    document.addEventListener('change', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        var fileInput = zone.querySelector('input[type="file"]');
        if (fileInput && e.target === fileInput && fileInput.files[0]) {
            uploadProductImage(fileInput.files[0]);
            fileInput.value = '';
        }
    });

    document.addEventListener('dragover', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (zone) { e.preventDefault(); zone.classList.add('drag-over'); }
    });

    document.addEventListener('dragleave', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (zone) zone.classList.remove('drag-over');
    });

    document.addEventListener('drop', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        e.preventDefault();
        zone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        if (file) uploadProductImage(file);
    });

    function uploadProductImage(file) {
        var zone = document.querySelector('.file-zone[data-field="product_images"]');
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
            var urls = getImageUrls();
            urls.push(d.data.url);
            setImageUrls(urls);
            zone.innerHTML = '<span><span class="upload-icon">☁️</span>Tap to add product image</span>';
        })
        .catch(function(e) {
            zone.innerHTML = '<span><span class="upload-icon">⚠️</span>Upload failed. Try again.</span>';
        });
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
