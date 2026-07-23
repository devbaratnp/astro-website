<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('मिडिया ग्यालेरी | Astro Shree Hari', 'भिडियो तथा फोटो ग्यालेरी — प्रवचन, भजन तथा कार्यक्रमका झलकहरू।', '/gallery', ['/assets/css/pages/gallery.css']);

$db = getDbConnection();
$items = [];
$hasImage = false; $hasVideo = false;
if ($db) {
    $stmt = $db->query("SELECT id, type, title_ne, title_en, url, thumbnail, embed_url, source FROM gallery_items WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 100");
    $items = $stmt->fetchAll();
    foreach ($items as $i) {
        if ($i['type'] === 'image') $hasImage = true;
        if ($i['type'] === 'video') $hasVideo = true;
    }
}
?>
<div class="section page-section">
  <div class="container">
    <div class="section-heading">
      <span>मिडिया ग्यालेरी</span>
      <h2>भिडियो तथा फोटो ग्यालेरी</h2>
      <p>प्रवचन, भजन तथा कार्यक्रमका झलकहरू</p>
    </div>

    <?php if ($hasImage || $hasVideo): ?>
      <div class="gallery-tabs">
        <button class="tab-btn active" data-type="all">सबै</button>
        <?php if ($hasVideo): ?>
          <button class="tab-btn" data-type="video"><?php echo renderIcon('Video'); ?> भिडियो</button>
        <?php endif; ?>
        <?php if ($hasImage): ?>
          <button class="tab-btn" data-type="image"><?php echo renderIcon('Image'); ?> फोटो</button>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="gallery-grid" id="gallery-grid">
      <?php foreach ($items as $item): ?>
        <div class="gallery-item" data-type="<?php echo $item['type']; ?>">
          <?php if ($item['type'] === 'video'): ?>
            <a href="<?php echo htmlspecialchars($item['embed_url'] ?: $item['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" class="gallery-video">
              <div class="gallery-thumb" style="background-image:<?php echo $item['thumbnail'] ? 'url(' . htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8') . ')' : 'none'; ?>">
                <span class="play-btn"><?php echo renderIcon('Play'); ?></span>
              </div>
              <div class="gallery-info">
                <strong><?php echo htmlspecialchars($item['title_ne'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <?php if (!empty($item['source'])): ?>
                  <small><?php echo htmlspecialchars($item['source'], ENT_QUOTES, 'UTF-8'); ?></small>
                <?php endif; ?>
              </div>
            </a>
          <?php else: ?>
            <div class="gallery-photo" data-url="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" data-title="<?php echo htmlspecialchars($item['title_ne'], ENT_QUOTES, 'UTF-8'); ?>">
              <img src="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" />
              <div class="gallery-info"><strong><?php echo htmlspecialchars($item['title_ne'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($items)): ?>
      <p style="text-align:center;padding:60px 0;color:var(--muted)">हाल कुनै मिडिया उपलब्ध छैन।</p>
    <?php endif; ?>

    <div class="lightbox" id="lightbox" style="display:none">
      <img id="lightbox-img" src="" alt="" />
      <button class="lightbox-close" id="lightbox-close"><?php echo renderIcon('X'); ?></button>
    </div>
  </div>
</div>
<?php renderPublicFooter(['/assets/js/gallery.js']); ?>
