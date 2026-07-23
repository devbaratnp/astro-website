<?php

require_once __DIR__ . '/includes/public-header.php';

$slug = $_GET['slug'] ?? '';

$db = getDbConnection();
$article = null;

if ($db && $slug) {
    $stmt = $db->prepare("SELECT id, title_ne, title_en, slug, content_ne, content_en, excerpt_ne, excerpt_en, cover_image, tags, published_at, updated_at FROM articles WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $article = $stmt->fetch();
}

if (!$article) {
    renderPublicHeader('लेख फेला परेन | Astro Shree Hari', 'अनुरोध गरिएको लेख फेला पार्न सकिएन। कृपया लेख सूचीमा फर्कनुहोस्।', '/blog', ['/assets/css/pages/blog.css']);
    ?>
    <section class="section page-section">
      <div class="container">
        <p class="empty-text">लेख फेला परेन।</p>
        <a href="/blog" class="button button-outline"><?php echo renderIcon('ArrowLeft'); ?> पछाडि जानुहोस्</a>
      </div>
    </section>
    <?php
    renderPublicFooter();
    exit;
}

$articleUrl = SITE_URL . '/article/' . htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8');
$articleDesc = !empty($article['excerpt_ne']) ? $article['excerpt_ne'] : $article['title_ne'];
$ogImage = !empty($article['cover_image']) ? $article['cover_image'] : null;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article['title_ne'],
    'description' => $articleDesc,
    'datePublished' => $article['published_at'],
    'author' => [
        '@type' => 'Person',
        'name' => 'पं. ज्यो. सीताराम तिमल्सेना',
        'url' => SITE_URL . '/about',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'श्रीहरि ज्योतिष परामर्श केन्द्र',
        'url' => SITE_URL,
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $articleUrl,
    ],
];

if (!empty($article['cover_image'])) {
    $jsonLd['image'] = $article['cover_image'];
}

renderPublicHeader(
    $article['title_ne'] . ' | Astro Shree Hari',
    $articleDesc,
    '/blog',
    ['/assets/css/pages/blog.css'],
    $ogImage,
    $jsonLd,
    'article'
);

$shareTitleAttr = htmlspecialchars($article['title_ne'], ENT_QUOTES, 'UTF-8');

if (!empty($article['tags'])) {
    $article['tags'] = json_decode($article['tags'], true);
}
?>

<article class="section page-section">
  <div class="container article-container">
    <a href="/blog" class="back-link"><?php echo renderIcon('ArrowLeft'); ?> लेखहरूमा फर्कनुहोस्</a>
    <div class="article-header">
      <h1><?php echo htmlspecialchars($article['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="article-meta">
        <span><?php echo renderIcon('CalendarBlank'); ?> <?php echo htmlspecialchars(substr($article['published_at'], 0, 10), ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
    <?php if (!empty($article['cover_image'])): ?>
    <img src="<?php echo htmlspecialchars($article['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($article['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" class="article-cover" />
    <?php endif; ?>
    <div class="article-content"><?php echo $article['content_ne']; ?></div>
    <div class="article-footer">
      <button class="button button-outline" onclick="if(navigator.share)navigator.share({title:'<?php echo $shareTitleAttr; ?>',url:window.location.href})"><?php echo renderIcon('ShareNetwork'); ?> सेयर गर्नुहोस्</button>
    </div>
  </div>
</article>

<?php

renderPublicFooter();
