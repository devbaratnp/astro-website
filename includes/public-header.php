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
    $navLinks = [
        '/' => 'गृहपृष्ठ',
        '/about' => 'हाम्रो बारेमा',
        '/services' => 'सेवाहरू',
        '/kundali' => 'कुण्डली',
        '/panchang' => 'पञ्चाङ्ग',
        '/muhurta' => 'मुहूर्त',
        '/blog' => 'लेख',
        '/events' => 'कार्यक्रम',
        '/gallery' => 'ग्यालेरी',
        '/pooja' => 'ई-पूजा',
        '/store' => 'पूजा भण्डार',
        '/appointment' => 'परामर्श प्रक्रिया',
        '/contact' => 'सम्पर्क',
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
            <?php foreach ($navLinks as $url => $label): ?>
              <?php $isActive = ($currentPage === $url || ($url !== '/' && str_starts_with($currentPage, $url))); ?>
              <a href="<?php echo $url; ?>" class="<?php echo $isActive ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
              </a>
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
