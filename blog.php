<?php

require_once __DIR__ . '/includes/public-header.php';

renderPublicHeader(
    'लेख तथा रचनाहरू | Astro Shree Hari',
    'शास्त्रीय ज्ञान, आध्यात्मिक चिन्तन र सनातन संस्कृतिका विविध आयाम — ज्योतिष, वास्तु, कर्मकाण्ड र आध्यात्मिक जीवन।',
    '/blog',
    ['/assets/css/pages/blog.css']
);

$db = getDbConnection();
if ($db) {
    $stmt = $db->query("SELECT id, title_ne, title_en, slug, excerpt_ne, excerpt_en, cover_image, content_ne, published_at FROM articles WHERE published_at IS NOT NULL ORDER BY published_at DESC LIMIT 50");
    $articles = $stmt->fetchAll();
} else {
    $articles = [];
}
?>

<section class="section page-section">
  <div class="container">
    <div class="section-heading">
      <span>साहित्य तथा सिर्जना</span>
      <h1>लेख तथा रचनाहरू</h1>
      <p>शास्त्रीय ज्ञान, आध्यात्मिक चिन्तन र सनातन संस्कृतिका विविध आयाम</p>
    </div>
    <?php if (!empty($articles)): ?>
    <div class="blog-grid">
      <?php foreach ($articles as $a): ?>
      <a href="/article/<?php echo htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="blog-card">
        <?php if (!empty($a['cover_image'])): ?>
        <div class="blog-cover"><img src="<?php echo htmlspecialchars($a['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($a['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" /></div>
        <?php endif; ?>
        <div class="blog-body">
          <h2><?php echo htmlspecialchars($a['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h2>
          <?php if (!empty($a['excerpt_ne'])): ?>
          <p><?php echo htmlspecialchars($a['excerpt_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php elseif (!empty($a['content_ne'])): ?>
          <p><?php echo htmlspecialchars(mb_substr(strip_tags($a['content_ne']), 0, 150), ENT_QUOTES, 'UTF-8'); ?>…</p>
          <?php endif; ?>
          <span class="blog-meta"><?php echo renderIcon('CalendarBlank'); ?> <?php echo htmlspecialchars(substr($a['published_at'], 0, 10), ENT_QUOTES, 'UTF-8'); ?></span>
          <strong class="blog-read">पूरा पढ्नुहोस् <?php echo renderIcon('ArrowRight'); ?></strong>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="empty-text">हाल कुनै लेख उपलब्ध छैनन्।</p>
    <?php endif; ?>
  </div>
</section>

<?php

renderPublicFooter();
