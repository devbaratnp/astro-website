<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('पूजा भण्डार | Astro Shree Hari', 'पूजा सामग्री, मूर्ति, धूप, अक्षता, फलफूल — सबै धार्मिक सामग्री एकै स्थानमा।', '/store', ['/assets/css/pages/gallery.css']);

$db = getDbConnection();
$products = [];
if ($db) {
    $stmt = $db->query("SELECT * FROM products ORDER BY category, id");
    $products = $stmt->fetchAll();
}
foreach ($products as &$p) {
    $p['images'] = json_decode($p['images'] ?? '[]', true);
}
unset($p);
$categories = [];
foreach ($products as $p) {
    if (!empty($p['category'])) {
        $categories[$p['category']][] = $p;
    }
}
?>
<div class="section page-section">
  <div class="container">
    <div class="section-heading">
      <span>Store</span>
      <h2>पूजा भण्डार</h2>
    </div>

    <?php if (empty($products)): ?>
      <p style="text-align:center;padding:60px 0;color:var(--muted)">हाल कुनै उत्पादन उपलब्ध छैन।</p>
    <?php else: ?>
      <?php foreach ($categories as $cat => $catProducts): ?>
        <div style="margin-bottom:32px">
          <h3 style="font-size:17px;color:var(--maroon);margin-bottom:12px;border-bottom:1px solid var(--gold);padding-bottom:6px"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></h3>
          <div class="service-grid">
            <?php foreach ($catProducts as $p): ?>
              <?php $img = $p['images'][0] ?? null; $out = $p['stock_status'] === 'out_of_stock'; ?>
              <article class="service-card" style="padding:0;text-align:left;overflow:hidden">
                <?php if ($img): ?>
                  <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:150px;object-fit:cover;display:block" loading="lazy" />
                <?php endif; ?>
                <div style="padding:<?php echo $img ? '14px 18px 18px' : '22px'; ?>;display:flex;flex-direction:column;flex:1">
                  <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap">
                    <?php if ($p['stock_status'] === 'in_stock'): ?>
                      <span class="badge badge-confirmed" style="font-size:9px">स्टकमा</span>
                    <?php elseif ($p['stock_status'] === 'pre_order'): ?>
                      <span class="badge badge-pending" style="font-size:9px">प्रि-अर्डर</span>
                    <?php else: ?>
                      <span class="badge badge-cancelled" style="font-size:9px">सकिएको</span>
                    <?php endif; ?>
                    <?php if (($p['compare_price'] ?? 0) > $p['price']): ?>
                      <span style="font-size:10px;color:#b33a3a;font-weight:600">-<?php echo round((1 - $p['price'] / $p['compare_price']) * 100); ?>%</span>
                    <?php endif; ?>
                  </div>
                  <h3 style="font-size:14px;margin-bottom:2px;font-family:inherit;color:var(--ink)"><?php echo htmlspecialchars($p['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <?php if (!empty($p['title_en'])): ?>
                    <small style="color:var(--muted);font-size:11px;margin-bottom:6px"><?php echo htmlspecialchars($p['title_en'], ENT_QUOTES, 'UTF-8'); ?></small>
                  <?php endif; ?>
                  <?php if (!empty($p['description_ne'])): ?>
                    <p style="font-size:12px;color:var(--ink);margin-bottom:6px;line-height:1.5"><?php echo htmlspecialchars($p['description_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endif; ?>
                  <div style="margin-top:auto">
                    <strong style="font-size:18px;color:var(--maroon);display:block;margin-bottom:8px">रु <?php echo number_format((float)$p['price']); ?></strong>
                    <?php if (($p['compare_price'] ?? 0) > $p['price']): ?>
                      <s style="color:var(--muted);font-size:12px;margin-left:8px">रु <?php echo number_format((float)$p['compare_price']); ?></s>
                    <?php endif; ?>
                    <button class="button button-maroon product-order-btn" style="width:100%;justify-content:center;opacity:<?php echo $out ? '.7' : '1'; ?>" <?php echo $out ? 'disabled' : ''; ?> data-product="<?php echo htmlspecialchars($p['title_ne'], ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo $out ? 'सकिएको' : 'अर्डर गर्नुहोस्'; ?>
                    </button>
                    <div class="order-contact-panel" style="display:none;margin-top:10px;padding:10px 12px;background:var(--cream);border-radius:6px;font-size:12px;line-height:1.8">
                      <strong>सम्पर्क गर्नुहोस्:</strong><br />
                      <?php echo renderIcon('Phone'); ?> <a href="tel:+<?php echo PHONE; ?>" style="color:var(--maroon)"><?php echo PHONE_DISPLAY; ?></a><br />
                      <?php echo renderIcon('WhatsappLogo'); ?> <a href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer" style="color:var(--maroon)">WhatsApp</a>
                    </div>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<?php renderPublicFooter(['/assets/js/store.js']); ?>
