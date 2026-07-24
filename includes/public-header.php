<?php

require_once __DIR__ . '/public-config.php';
require_once __DIR__ . '/public-icons.php';
require_once __DIR__ . '/public-seo.php';
require_once __DIR__ . '/public-footer.php';

/**
 * Render standard public page header
 */
function renderPublicHeader(
    string $title,
    string $description,
    string $currentPage = '/',
    array $extraCss = [],
    ?string $ogImage = null,
    ?array $jsonLd = null,
    string $ogType = 'website'
): void {
    $navItems = [
        ['url' => '/',           'label' => 'गृहपृष्ठ'],
        ['url' => '/about',      'label' => 'हाम्रो बारेमा'],
        ['url' => '/services',   'label' => 'सेवाहरू', 'dropdown' => [
            ['url' => '/kundali',   'label' => 'कुण्डली'],
            ['url' => '/panchang',  'label' => 'पञ्चाङ्ग'],
            ['url' => '/muhurta',   'label' => 'मुहूर्त'],
            ['url' => '/pooja',     'label' => 'ई-पूजा'],
            ['url' => '/store',     'label' => 'पूजा भण्डार'],
        ]],
        ['url' => '/blog',       'label' => 'लेख'],
        ['url' => '/gallery',    'label' => 'ग्यालेरी'],
        ['url' => '/appointment','label' => 'परामर्श'],
        ['url' => '/contact',    'label' => 'सम्पर्क'],
    ];
    ?>
<!doctype html>
<html lang="ne">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php renderSeo($title, $description, $currentPage, $ogImage, $jsonLd, $ogType); ?>
    <link rel="icon" type="image/png" href="<?php echo assetUrl('/assets/shreehari-icon-192.png'); ?>" />
    <link rel="apple-touch-icon" href="<?php echo assetUrl('/assets/shreehari-icon-192.png'); ?>" />
    <link rel="stylesheet" href="<?php echo assetUrl('/assets/css/site.css'); ?>" />
    <?php foreach ($extraCss as $cssFile): ?>
      <link rel="stylesheet" href="<?php echo htmlspecialchars(strpos($cssFile, '/') === 0 ? BASE_PATH . $cssFile : $cssFile, ENT_QUOTES, 'UTF-8'); ?>" />
    <?php endforeach; ?>
  </head>
  <body>
    <div class="site-shell">
      <header class="header">
        <div class="nav-wrap">
          <a to="/" href="/" class="brand" aria-label="Astro Shree Hari home">
            <img class="brand-logo" src="<?php echo assetUrl('/assets/shreehari-logo.webp'); ?>" alt="श्रीहरि ज्योतिष लोगो" width="56" height="56" />
            <span>
              <strong>Astro Shree Hari</strong>
              <small>श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल</small>
            </span>
          </a>
          <button type="button" class="menu-button" id="mobile-menu-btn" aria-label="Menu" aria-expanded="false">
            <?php echo renderIcon('List'); ?>
          </button>
          <nav class="nav" id="main-nav">
            <?php foreach ($navItems as $item): ?>
              <?php if (!empty($item['dropdown'])): ?>
              <div class="nav-dropdown">
                <a href="<?php echo $item['url']; ?>" class="nav-dropdown-trigger <?php echo (str_starts_with($currentPage, $item['url']) && $item['url'] !== '/') ? 'active' : ''; ?>">
                  <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                  <svg class="dd-arrow" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </a>
                <div class="nav-dropdown-menu">
                  <?php foreach ($item['dropdown'] as $sub): ?>
                  <a href="<?php echo $sub['url']; ?>" class="<?php echo ($currentPage === $sub['url']) ? 'active' : ''; ?>"><?php echo htmlspecialchars($sub['label'], ENT_QUOTES, 'UTF-8'); ?></a>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <a href="<?php echo $item['url']; ?>" class="<?php echo ($currentPage === $item['url'] || ($item['url'] !== '/' && str_starts_with($currentPage, $item['url']))) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
              </a>
              <?php endif; ?>
            <?php endforeach; ?>
          </nav>
          <a class="phone-link" href="tel:+<?php echo PHONE; ?>">
            <?php echo renderIcon('Phone'); ?> <?php echo PHONE_DISPLAY; ?>
          </a>
          <a class="button button-gold nav-cta" href="/appointment">परामर्श बुक गर्नुहोस्</a>
        </div>
      </header>
      <main>
    <?php
}
