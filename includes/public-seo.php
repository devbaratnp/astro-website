<?php

function renderSeo(
    string $title,
    string $description,
    string $path = '/',
    ?string $ogImage = null,
    ?array $jsonLd = null,
    string $ogType = 'website'
): void {
    $canonicalUrl = rtrim(SITE_URL, '/') . ($path === '/' ? '' : $path);
    $image = $ogImage ?: (SITE_URL . '/assets/shreehari-logo.webp');

    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeDesc = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8');
    $safeImage = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');

    echo "    <title>{$safeTitle}</title>\n";
    echo "    <meta name=\"description\" content=\"{$safeDesc}\" />\n";
    echo "    <link rel=\"canonical\" href=\"{$safeUrl}\" />\n";
    echo "    <meta name=\"robots\" content=\"index, follow, max-snippet:-1, max-image-preview:large\" />\n";
    echo "    <meta property=\"og:title\" content=\"{$safeTitle}\" />\n";
    echo "    <meta property=\"og:description\" content=\"{$safeDesc}\" />\n";
    echo "    <meta property=\"og:url\" content=\"{$safeUrl}\" />\n";
    echo "    <meta property=\"og:type\" content=\"{$ogType}\" />\n";
    echo "    <meta property=\"og:locale\" content=\"ne_NP\" />\n";
    echo "    <meta property=\"og:site_name\" content=\"श्रीहरि ज्योतिष परामर्श केन्द्र\" />\n";
    echo "    <meta property=\"og:image\" content=\"{$safeImage}\" />\n";
    echo "    <meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
    echo "    <meta name=\"twitter:title\" content=\"{$safeTitle}\" />\n";
    echo "    <meta name=\"twitter:description\" content=\"{$safeDesc}\" />\n";
    echo "    <meta name=\"twitter:image\" content=\"{$safeImage}\" />\n";

    if ($jsonLd !== null) {
        echo "    <script type=\"application/ld+json\">\n" . json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n    </script>\n";
    } else {
        $defaultSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'श्रीहरि ज्योतिष परामर्श केन्द्र',
            'alternateName' => ['Astro Shree Hari', 'Shreehari Jyotish Paramarsha Kendra'],
            'url' => SITE_URL,
            'logo' => SITE_URL . '/assets/shreehari-logo.webp',
            'telephone' => '+9779844639228',
            'email' => EMAIL,
            'priceRange' => '$$',
            'founder' => [
                '@type' => 'Person',
                'name' => 'Sitaram Timsina',
                'jobTitle' => 'Astrologer'
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Kamal-3, Kerkha',
                'addressLocality' => 'Jhapa',
                'addressCountry' => 'NP'
            ]
        ];
        echo "    <script type=\"application/ld+json\">\n" . json_encode($defaultSchema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n    </script>\n";
    }
}
