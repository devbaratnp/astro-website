<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

$base = 'https://www.astroshreehari.com';

$staticPages = [
    '/' => ['priority' => '1.0'],
    '/about' => [],
    '/services' => [],
    '/blog' => ['changefreq' => 'weekly', 'priority' => '0.8'],
    '/events' => ['changefreq' => 'weekly', 'priority' => '0.7'],
    '/gallery' => ['priority' => '0.6'],
    '/muhurta' => ['priority' => '0.7'],
    '/appointment' => ['priority' => '0.9'],
    '/kundali' => ['priority' => '0.9'],
    '/pooja' => ['priority' => '0.9'],
    '/panchang' => ['changefreq' => 'daily', 'priority' => '0.9'],
    '/payment' => [],
    '/contact' => [],
];

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($staticPages as $path => $attrs) {
    echo '<url><loc>', $base, $path, '</loc>';
    if (!empty($attrs['changefreq'])) echo '<changefreq>', $attrs['changefreq'], '</changefreq>';
    if (!empty($attrs['priority'])) echo '<priority>', $attrs['priority'], '</priority>';
    echo '</url>';
}

try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT slug, published_at FROM articles ORDER BY published_at DESC");
    while ($row = $stmt->fetch()) {
        echo '<url><loc>', $base, '/article/', htmlspecialchars($row['slug']), '</loc>';
        echo '<lastmod>', substr($row['published_at'], 0, 10), '</lastmod>';
        echo '<changefreq>monthly</changefreq><priority>0.7</priority></url>';
    }
} catch (Exception $e) {}

echo '</urlset>';
