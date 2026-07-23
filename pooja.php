<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('ई-पूजा तथा पुरोहित बुकिङ | Astro Shree Hari', 'वैदिक पूजा, होम, संस्कार र जीवनका सम्पूर्ण कर्मकाण्ड सेवा अनलाइन बुक गर्नुहोस्।', '/pooja', ['/assets/css/pages/forms.css', '/assets/css/pages/gallery.css']);

$db = getDbConnection();
$services = [];
if ($db) {
    $stmt = $db->query("SELECT id, title_ne, title_en, description_ne, description_en, base_price FROM pooja_services ORDER BY id");
    $services = $stmt->fetchAll();
}
?>
<div class="section page-section">
  <div class="container" style="padding-top:40px">
    <div class="section-heading">
      <span>E-Pooja</span>
      <h2>पूजा तथा पुरोहित बुकिङ</h2>
    </div>

    <?php if (empty($services)): ?>
      <p style="text-align:center;color:var(--muted);font-size:16px;margin-top:20px">हाल कुनै सेवा उपलब्ध छैन।</p>
    <?php else: ?>
    <div class="service-grid" id="service-grid">
      <?php foreach ($services as $s): ?>
        <article class="service-card" data-id="<?php echo $s['id']; ?>">
          <h3><?php echo htmlspecialchars($s['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <p><?php echo htmlspecialchars($s['description_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
          <strong>रु <?php echo $s['base_price'] ? htmlspecialchars($s['base_price'], ENT_QUOTES, 'UTF-8') : 'सम्पर्क गर्नुहोस्'; ?></strong>
          <button type="button" class="text-link download-materials" data-id="<?php echo $s['id']; ?>" data-title="<?php echo htmlspecialchars($s['title_ne'], ENT_QUOTES, 'UTF-8'); ?>">सामग्री सूची डाउनलोड</button>
          <button type="button" class="button button-maroon book-btn" data-id="<?php echo $s['id']; ?>" data-title="<?php echo htmlspecialchars($s['title_ne'], ENT_QUOTES, 'UTF-8'); ?>">बुक गर्नुहोस्</button>
        </article>
      <?php endforeach; ?>
    </div>

    <form class="booking-form feature-form" id="booking-form" style="display:none">
      <h3 id="form-service-title"></h3>
      <input type="hidden" name="service_id" id="form-service-id" />
      <div class="form-grid">
        <label>नाम *<input type="text" name="name" required /></label>
        <label>फोन *<input type="tel" name="phone" required /></label>
        <label>इमेल<input type="email" name="email" /></label>
        <label>मिति *<input type="date" name="preferred_date" required id="preferred-date" /></label>
        <label>समय<input type="time" name="preferred_time" /></label>
        <label>ठेगाना<input type="text" name="address" /></label>
        <label class="full"><input type="checkbox" name="needs_materials" value="1" /> पूजा सामग्री घरमै चाहिन्छ</label>
        <label class="full"><input type="checkbox" name="is_live_stream" value="1" /> Live streamed पूजा हेर्न चाहन्छु</label>
        <label class="full">विशेष निर्देशन<textarea name="special_instructions"></textarea></label>
      </div>
      <button type="submit" class="button button-maroon full-button" id="submit-btn">पूजा बुक गर्नुहोस्</button>
      <div id="success-msg" class="success" style="display:none"></div>
      <div id="error-msg" class="form-error" style="display:none"></div>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php renderPublicFooter(['/assets/js/pooja.js']); ?>
