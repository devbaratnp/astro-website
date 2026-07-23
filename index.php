<?php
require_once __DIR__ . '/includes/public-config.php';
require_once __DIR__ . '/includes/public-header.php';
require_once __DIR__ . '/includes/public-footer.php';
require_once __DIR__ . '/includes/public-icons.php';

$timezone = new DateTimeZone('Asia/Kathmandu');
$today = new DateTime('now', $timezone);
$todayEn = clone $today;

$BS_DATA = [
    1970=>[31,31,32,32,31,30,30,29,30,29,30,30],1971=>[31,32,31,32,31,30,30,30,29,29,30,31],
    1972=>[30,32,31,32,31,30,30,30,29,30,29,31],1973=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1974=>[31,31,32,32,31,30,30,29,30,29,30,30],1975=>[31,32,31,32,31,30,30,30,29,29,30,31],
    1976=>[30,32,31,32,31,30,30,30,29,30,29,31],1977=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1978=>[31,31,32,32,31,30,30,29,30,29,30,30],1979=>[31,32,31,32,31,30,30,30,29,29,30,31],
    1980=>[31,31,31,32,31,31,29,30,29,30,29,31],1981=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1982=>[31,31,32,32,31,30,30,29,30,29,30,30],1983=>[31,32,31,32,31,30,30,30,29,29,30,31],
    1984=>[31,31,31,32,31,31,29,30,30,29,30,30],1985=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1986=>[31,31,32,32,31,30,30,29,30,29,30,30],1987=>[31,32,31,32,31,30,30,30,29,29,30,31],
    1988=>[31,31,31,32,31,31,29,30,30,29,30,30],1989=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1990=>[31,32,31,32,31,30,30,29,30,29,30,30],1991=>[31,32,31,32,31,30,30,30,29,30,29,31],
    1992=>[31,31,31,32,31,31,30,29,30,29,30,30],1993=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1994=>[31,32,31,32,31,30,30,30,29,29,30,30],1995=>[31,32,31,32,31,30,30,30,29,30,29,31],
    1996=>[31,31,32,31,31,31,30,29,30,29,30,30],1997=>[31,31,32,31,31,31,30,29,30,29,30,30],
    1998=>[31,32,31,32,31,30,30,30,29,29,30,30],1999=>[31,32,31,32,31,30,30,30,29,30,29,31],
    2000=>[31,31,32,31,31,31,30,29,30,29,30,30],2001=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2002=>[31,32,31,32,31,30,30,30,29,29,30,31],2003=>[30,32,31,32,31,30,30,30,29,30,29,31],
    2004=>[31,31,32,31,31,31,30,29,30,29,30,30],2005=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2006=>[31,32,31,32,31,30,30,30,29,29,30,31],2007=>[31,31,31,32,31,31,29,30,30,29,29,31],
    2008=>[31,31,32,31,31,31,30,29,30,29,30,30],2009=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2010=>[31,32,31,32,31,30,30,30,29,29,30,31],2011=>[31,31,31,32,31,31,29,30,30,29,30,30],
    2012=>[31,31,32,31,31,31,30,29,30,29,30,30],2013=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2014=>[31,32,31,32,31,30,30,30,29,29,30,31],2015=>[31,31,31,32,31,31,29,30,30,29,30,30],
    2016=>[31,31,32,31,31,31,30,29,30,29,30,30],2017=>[31,32,31,32,31,30,30,29,30,29,30,30],
    2018=>[31,32,31,32,31,30,30,30,29,30,29,31],2019=>[31,31,31,32,31,31,30,29,30,29,30,30],
    2020=>[31,31,32,31,31,31,30,29,30,29,30,30],2021=>[31,32,31,32,31,30,30,30,29,29,30,30],
    2022=>[31,32,31,32,31,30,30,30,29,30,29,31],2023=>[31,31,31,32,31,31,30,29,30,29,30,30],
    2024=>[31,31,32,31,31,31,30,29,30,29,30,30],2025=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2026=>[30,32,31,32,31,30,30,30,29,30,29,31],2027=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2028=>[31,31,32,31,32,30,30,29,30,29,30,30],2029=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2030=>[30,32,31,32,31,30,30,30,29,30,29,31],2031=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2032=>[31,31,32,32,31,30,30,29,30,29,30,30],2033=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2034=>[30,32,31,32,31,31,29,30,30,29,29,31],2035=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2036=>[31,31,32,32,31,30,30,29,30,29,30,30],2037=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2038=>[31,31,31,32,31,31,29,30,30,29,30,30],2039=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2040=>[31,31,32,32,31,30,30,29,30,29,30,30],2041=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2042=>[31,31,31,32,31,31,29,30,30,29,30,30],2043=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2044=>[31,32,31,32,31,30,30,29,30,29,30,30],2045=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2046=>[31,31,31,32,31,31,30,29,30,29,30,30],2047=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2048=>[31,32,31,32,31,30,30,30,29,29,30,30],2049=>[31,32,31,32,31,30,30,30,29,30,29,31],
    2050=>[31,31,31,32,31,31,30,29,30,29,30,30],2051=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2052=>[31,32,31,32,31,30,30,30,29,29,30,30],2053=>[31,32,31,32,31,30,30,30,29,30,29,31],
    2054=>[31,31,32,31,31,31,30,29,30,29,30,30],2055=>[31,31,32,31,32,30,30,29,30,29,30,30],
    2056=>[31,32,31,32,31,30,30,30,29,29,30,31],2057=>[30,32,31,32,31,30,30,30,29,30,29,31],
    2058=>[31,31,32,31,31,31,30,29,30,29,30,30],2059=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2060=>[31,32,31,32,31,30,30,30,29,29,30,31],2061=>[31,31,31,32,31,31,29,30,29,30,29,31],
    2062=>[31,31,32,31,31,31,30,29,30,29,30,30],2063=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2064=>[31,32,31,32,31,30,30,30,29,29,30,31],2065=>[31,31,31,32,31,31,29,30,30,29,29,31],
    2066=>[31,31,32,31,31,31,30,29,30,29,30,30],2067=>[31,31,32,32,31,30,30,29,30,29,30,30],
    2068=>[31,32,31,32,31,30,30,30,29,29,30,31],2069=>[31,31,31,32,31,31,29,30,30,29,30,30],
    2070=>[31,31,32,31,31,31,30,29,30,29,30,30],2071=>[31,32,31,32,31,30,30,29,30,29,30,30],
    2072=>[31,32,31,32,31,30,30,30,29,29,30,31],2073=>[31,31,31,32,31,31,30,29,30,29,30,30],
    2074=>[31,31,32,31,31,31,30,29,30,29,30,30],2075=>[31,32,31,32,31,30,30,30,29,29,30,30],
    2076=>[31,32,31,32,31,30,30,30,29,30,29,31],2077=>[31,31,31,32,31,31,30,29,30,29,30,30],
    2078=>[31,31,32,31,31,31,30,29,30,29,30,30],2079=>[31,32,31,32,31,30,30,30,29,29,30,30],
    2080=>[31,32,31,32,31,30,30,30,29,30,29,31],2081=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2082=>[31,31,32,31,31,31,30,29,30,29,30,30],2083=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2084=>[30,32,31,32,31,30,30,30,29,30,29,31],2085=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2086=>[31,31,32,32,31,30,30,29,30,29,30,30],2087=>[31,32,31,32,31,30,30,30,29,29,30,31],
    2088=>[30,32,31,32,31,31,29,30,29,30,29,31],2089=>[31,31,32,31,31,31,30,29,30,29,30,30],
    2090=>[31,31,32,32,31,30,30,29,30,29,30,30],
];

$bsMonths = ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];
$nepaliWeekdays = ['आइतबार', 'सोमबार', 'मङ्गलबार', 'बुधबार', 'बिहिबार', 'शुक्रबार', 'शनिबार'];
$gregMonthsNe = ['जनवरी', 'फेब्रुअरी', 'मार्च', 'अप्रिल', 'मे', 'जुन', 'जुलाई', 'अगस्ट', 'सेप्टेम्बर', 'अक्टोबर', 'नोभेम्बर', 'डिसेम्बर'];

function bsYearDaysHelper($y, $data) {
    return isset($data[$y]) ? array_sum($data[$y]) : 365;
}

function bsMonthDaysHelper($y, $m, $data) {
    return isset($data[$y]) ? $data[$y][$m] : 30;
}

function ad2bs(DateTime $dt, array $data): ?array {
    $BASE_AD = new DateTime('1918-04-13');
    $BS_START = 1975;
    $BS_MAX_YEAR = 2090;
    $BS_MIN_YEAR = 1970;
    $tgt = clone $dt;
    $tgt->setTime(0, 0, 0);
    $base = clone $BASE_AD;
    $base->setTime(0, 0, 0);
    $df = (int)$tgt->diff($base)->days;
    $y = $BS_START;
    if ($df >= 0) {
        while (true) {
            $yd = bsYearDaysHelper($y, $data);
            if ($df < $yd) break;
            $df -= $yd;
            $y++;
            if ($y > $BS_MAX_YEAR + 100) break;
        }
    } else {
        while ($df < 0) {
            $y--;
            $df += bsYearDaysHelper($y, $data);
            if ($y < $BS_MIN_YEAR - 100) break;
        }
    }
    $m = 1;
    for ($i = 0; $i < 12; $i++) {
        $md = bsMonthDaysHelper($y, $i, $data);
        if ($df < $md) {
            $m = $i + 1;
            break;
        }
        $df -= $md;
    }
    return ['y' => $y, 'm' => $m, 'd' => $df + 1];
}

$bs = ad2bs($today, $BS_DATA);
$bsDateStr = $bs ? ($bs['y'] . ' ' . $bsMonths[$bs['m'] - 1] . ' ' . $bs['d']) : 'उपलब्ध छैन';

$w = (int)$todayEn->format('w');
$nepaliWd = $nepaliWeekdays[$w];
$monthName = $gregMonthsNe[(int)$todayEn->format('n') - 1];
$dayNum = strtr($todayEn->format('j'), '0123456789', '०१२३४५६७८९');
$yearNum = strtr($todayEn->format('Y'), '0123456789', '०१२३४५६७८९');
$todayLabel = $nepaliWd . ', ' . $monthName . ' ' . $dayNum . ', ' . $yearNum;

$clockInit = $todayEn->format('H:i:s');

$panchangItems = [];
$panchangHasData = false;
$articles = [];
$testimonials = [];
$services = getServicesList();

try {
    require_once __DIR__ . '/backend/lib/Panchang.php';
    $panchangData = Panchang::getForDate(date('Y-m-d'));
    if ($panchangData && !empty($panchangData['tithi'])) {
        $panchangHasData = true;
        $fields = [
            'tithi' => 'तिथि',
            'nakshatra' => 'नक्षत्र',
            'moon_rashi' => 'चन्द्र राशि',
            'sunrise' => 'सूर्योदय',
            'sunset' => 'सूर्यास्त',
        ];
        foreach ($fields as $key => $label) {
            $val = $panchangData[$key] ?? null;
            if ($val === null || $val === '') continue;
            if ($key === 'sunrise' || $key === 'sunset') {
                $val = substr($val, 0, 5);
            }
            $panchangItems[] = ['key' => $key, 'label' => $label, 'value' => $val];
        }
        if (empty($panchangItems)) $panchangHasData = false;
    }
    $db = getDbConnection();
    if ($db) {
        $aStmt = $db->prepare("SELECT id, title_ne, slug, excerpt_ne, cover_image FROM articles WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
        $aStmt->execute();
        $articles = $aStmt->fetchAll();
        $tStmt = $db->prepare("SELECT id, name, content, location FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 20");
        $tStmt->execute();
        $testimonials = $tStmt->fetchAll();
    }
} catch (Throwable $e) {
    error_log('Home page data error: ' . $e->getMessage());
}

renderPublicHeader(
    'श्रीहरि ज्योतिष परामर्श केन्द्र नेपाल — वैदिक ज्योतिष, वास्तु तथा कर्मकाण्ड',
    'धर्मशास्त्र, कर्मकाण्ड तथा ज्योतिषशास्त्रमा आधारित शास्त्रसम्मत सेवा। जन्मकुण्डली, विवाह मिलान, वास्तु, ग्रह शान्ति, पूजा तथा अनलाइन परामर्श।',
    '/',
    ['/assets/css/pages/home.css', '/assets/css/pages/about.css', '/assets/css/pages/blog.css']
);

$homeTools = [
    ['to' => '/panchang', 'icon' => 'CalendarDots', 'title' => 'आजको पञ्चाङ्ग', 'text' => 'तिथि, नक्षत्र, सूर्योदय, सूर्यास्त र दैनिक राशिफल हेर्नुहोस्।', 'link' => 'आजको विवरण'],
    ['to' => '/kundali', 'icon' => 'ChartPolar', 'title' => 'जन्मकुण्डली', 'text' => 'आफ्नो जन्म विवरणका आधारमा प्रारम्भिक ज्योतिषीय विवरण पाउनुहोस्।', 'link' => 'कुण्डली बनाउनुहोस्'],
    ['to' => '/pooja', 'icon' => 'Campfire', 'title' => 'पूजा तथा मुहूर्त', 'text' => 'वैदिक पूजा सेवा, अनुष्ठान र शुभ समयसम्बन्धी जानकारी लिनुहोस्।', 'link' => 'सेवा हेर्नुहोस्'],
    ['to' => '/appointment', 'icon' => 'Heart', 'title' => 'विवाह तथा परामर्श', 'text' => 'विवाह मिलान वा व्यक्तिगत जिज्ञासाका लागि गुरुज्यूसँग समय लिनुहोस्।', 'link' => 'परामर्श बुक गर्नुहोस्'],
];

?>
      <section id="home" class="hero">
        <div class="container hero-grid">
          <div class="hero-copy">
            <span class="eyebrow">शास्त्रसम्मत ज्योतिषीय परामर्श</span>
            <h1>तपाईँको जिज्ञासा हाम्रो परामर्श<br /><em>श्रीहरि ज्योतिष परामर्श केन्द्र नेपाल</em></h1>
            <p class="hero-name-en">Nepali Astrologer Sitaram Timalsena</p>
            <p>धर्मशास्त्र, कर्मकाण्ड तथा ज्योतिषशास्त्रसँग सम्बन्धित गुरुकुलीय पद्धति अनुसारको अध्ययन र अध्यापनको लामो अनुभव।</p>
            <div class="hero-actions">
              <a class="button button-maroon" href="/appointment"><?php echo renderIcon('CalendarBlank'); ?> परामर्श बुक गर्नुहोस्</a>
              <a class="button button-outline" href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer"><?php echo renderIcon('WhatsappLogo'); ?> WhatsApp मा सम्पर्क गर्नुहोस्</a>
            </div>
            <div class="rating"><span class="avatars"><i>श्री</i><i>ॐ</i><i>शुभ</i></span><span>विश्वसनीय धार्मिक तथा ज्योतिषीय सेवा</span></div>
          </div>
          <div class="portrait-wrap">
            <div class="portrait-ring"></div>
            <img src="<?php echo assetUrl('/assets/sitaram-timilsina.jpeg'); ?>" alt="पं. ज्यो. सीताराम तिमल्सेना" />
            <div class="name-plaque"><strong>पं. ज्यो. सीताराम तिमल्सेना</strong><span>नेपाली ज्योतिष तथा कर्मकाण्ड विशेषज्ञ</span></div>
          </div>
        </div>
      </section>

      <section class="trust-bar container">
        <div><?php echo renderIcon('GraduationCap'); ?><span><strong>गुरुकुलीय पद्धति</strong>अध्ययन तथा अध्यापन</span></div>
        <div><?php echo renderIcon('BookOpenText'); ?><span><strong>१८ महापुराण</strong>अध्ययन तथा वाचन</span></div>
        <div><?php echo renderIcon('UsersThree'); ?><span><strong>केन्द्रीय सदस्य</strong>दक्षिण एसियाली ज्योतिष महासङ्घ</span></div>
      </section>

      <section class="section astro-hub-section" aria-labelledby="astro-hub-title">
        <div class="container">
          <div class="astro-hub-heading">
            <div>
              <span class="section-kicker">दैनिक मार्गदर्शन र उपयोगी सेवा</span>
              <h2 id="astro-hub-title">आजको ज्योतिष एकै स्थानमा</h2>
              <p>दैनिक पञ्चाङ्ग हेर्नुहोस्, आवश्यक ज्योतिषीय सेवा छान्नुहोस् र विस्तृत परामर्शका लागि सहज रूपमा अगाडि बढ्नुहोस्।</p>
            </div>
            <a class="button button-maroon" href="/appointment"><?php echo renderIcon('CalendarBlank'); ?> परामर्श बुक गर्नुहोस्</a>
          </div>

          <div class="astro-hub-layout">
            <article class="astro-daily-card">
              <div class="astro-daily-topline">
                <div class="astro-daily-symbol"><?php echo renderIcon('Planet'); ?></div>
                <div>
                  <span>आजको पञ्चाङ्ग</span>
                  <strong class="astro-bs-date"><?php echo htmlspecialchars($bsDateStr, ENT_QUOTES, 'UTF-8'); ?></strong>
                  <span class="astro-en-date"><?php echo htmlspecialchars($todayLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="astro-clock"><?php echo htmlspecialchars($clockInit, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
              </div>

              <div class="astro-panchang-status" aria-live="polite">
                <?php if ($panchangHasData): ?>
                <div class="astro-panchang-grid">
                  <?php foreach ($panchangItems as $item): ?>
                  <div class="astro-panchang-item astro-<?php echo htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <strong><?php echo htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8'); ?></strong>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="astro-error">
                  <p>अहिले पञ्चाङ्ग प्राप्त गर्न सकिएन।</p>
                  <button type="button" onclick="window.location.reload()">फेरि प्रयास गर्नुहोस्</button>
                </div>
                <?php endif; ?>
              </div>

              <a class="astro-daily-link" href="/panchang">पूर्ण पञ्चाङ्ग र १२ राशिको राशिफल <?php echo renderIcon('ArrowRight'); ?></a>
              <small>पञ्चाङ्ग विवरण सामान्य जानकारीका लागि हो। व्यक्तिगत निर्णयका लागि विशेषज्ञ परामर्श लिनुहोस्।</small>
            </article>

            <div class="astro-tool-grid">
              <?php foreach ($homeTools as $tool): ?>
              <a class="astro-tool-card" href="<?php echo htmlspecialchars($tool['to'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="astro-tool-icon"><?php echo renderIcon($tool['icon']); ?></span>
                <div>
                  <h3><?php echo htmlspecialchars($tool['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p><?php echo htmlspecialchars($tool['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <strong><?php echo htmlspecialchars($tool['link'], ENT_QUOTES, 'UTF-8'); ?> <?php echo renderIcon('ArrowRight'); ?></strong>
                </div>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>

      <section id="services" class="section services-section">
        <div class="container">
          <div class="section-heading">
            <span>हाम्रा प्रमुख सेवाहरू</span>
            <h2>जीवनका हरेक पक्षका लागि वैदिक समाधान</h2>
            <p>शास्त्रसम्मत विधि, अनुभव र गोपनीयतामा आधारित व्यक्तिगत सेवा</p>
          </div>
          <div class="service-grid">
            <?php foreach ($services as $svc): ?>
            <article class="service-card">
              <?php echo renderIcon($svc['icon']); ?>
              <h3><?php echo htmlspecialchars($svc['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p><?php echo htmlspecialchars($svc['text'], ENT_QUOTES, 'UTF-8'); ?></p>
              <a href="/appointment">परामर्श लिनुहोस् <?php echo renderIcon('ArrowRight'); ?></a>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section id="about" class="section about-section">
        <div class="container about-grid">
          <div class="about-photo org-logo-panel">
            <img src="<?php echo assetUrl('/assets/shreehari-logo.webp'); ?>" alt="श्रीहरि ज्योतिष परामर्श केन्द्र नेपालको लोगो" />
            <div class="organization-badge"><strong>श्रीहरि</strong><span>ज्योतिष परामर्श केन्द्र नेपाल</span></div>
          </div>
          <div class="about-copy">
            <span class="section-kicker">हाम्रो बारेमा</span>
            <h2>शास्त्रीय परम्परा र विश्वसनीय परामर्शको केन्द्र</h2>
            <p>श्रीहरि ज्योतिष परामर्श केन्द्र नेपाल वैदिक ज्योतिष, वास्तुशास्त्र, कर्मकाण्ड र आध्यात्मिक सेवाका लागि समर्पित संस्था हो। पण्डित तथा ज्योतिषी सीताराम तिमल्सेनाको नेतृत्वमा केन्द्रले शास्त्रसम्मत ज्ञानलाई सरल, व्यावहारिक र सेवाग्राहीको जीवनसँग उपयोगी हुने गरी प्रस्तुत गर्दछ।</p>
            <p>नेपालस्थित प्रधान कार्यालय र अमेरिकास्थित अन्तर्राष्ट्रिय अनलाइन कार्यालयमार्फत देश–विदेशका सेवाग्राहीलाई प्रत्यक्ष तथा अनलाइन परामर्श उपलब्ध छ।</p>
            <ul>
              <li><?php echo renderIcon('CheckCircle'); ?> जन्मकुण्डली, विवाह मिलान तथा ग्रहगोचर विश्लेषण</li>
              <li><?php echo renderIcon('CheckCircle'); ?> वास्तु परामर्श, पूजा तथा वैदिक कर्मकाण्ड</li>
              <li><?php echo renderIcon('CheckCircle'); ?> महापुराण वाचन र तीर्थयात्रा सहजीकरण</li>
              <li><?php echo renderIcon('CheckCircle'); ?> नेपाल तथा विदेशमा अनलाइन परामर्श सेवा</li>
            </ul>
            <a class="text-link" href="/about">संस्था र गुरुज्यूको विस्तृत परिचय <?php echo renderIcon('ArrowRight'); ?></a>
          </div>
        </div>
      </section>

      <section class="credentials">
        <div class="container credentials-grid">
          <div><?php echo renderIcon('ShieldCheck'); ?><h3>शास्त्रसम्मत मार्गदर्शन</h3><p>धर्मशास्त्रीय आधार र परम्परागत वैदिक विधिमा आधारित सेवा।</p></div>
          <div><?php echo renderIcon('Monitor'); ?><h3>अनलाइन सेवा</h3><p>देश वा विदेशबाट WhatsApp मार्फत सहज परामर्श।</p></div>
          <div><?php echo renderIcon('LockKey'); ?><h3>पूर्ण गोपनीयता</h3><p>तपाईंको व्यक्तिगत विवरण र परामर्श पूर्ण रूपमा सुरक्षित।</p></div>
        </div>
      </section>

      <section id="process" class="section process-section">
        <div class="container">
          <div class="section-heading"><span>सरल र सहज</span><h2>परामर्श प्रक्रिया</h2></div>
          <div class="steps">
            <div><b>०१</b><?php echo renderIcon('CalendarBlank'); ?><h3>समय छान्नुहोस्</h3><p>उपलब्ध समयअनुसार आफ्नो समय बुक गर्नुहोस्</p></div>
            <div><b>०२</b><?php echo renderIcon('BookOpenText'); ?><h3>विवरण पठाउनुहोस्</h3><p>जन्म विवरण र आफ्नो मुख्य प्रश्न लेख्नुहोस्</p></div>
            <div><b>०३</b><?php echo renderIcon('ChartPolar'); ?><h3>विश्लेषण</h3><p>गुरुज्यूले शास्त्रसम्मत अध्ययन गर्नुहुन्छ</p></div>
            <div><b>०४</b><?php echo renderIcon('WhatsappLogo'); ?><h3>परामर्श सत्र</h3><p>प्रत्यक्ष वा अनलाइन परामर्श लिनुहोस्</p></div>
          </div>
        </div>
      </section>

<?php if (!empty($articles)): ?>
      <section class="section blog-section">
        <div class="container">
          <div class="section-heading">
            <span>लेख तथा रचनाहरू</span>
            <h2>हाम्रा हालैका लेखहरू</h2>
            <p>ज्योतिष, वास्तु, कर्मकाण्ड र आध्यात्मिक जीवनका विविध आयाम</p>
          </div>
          <div class="blog-grid">
            <?php foreach ($articles as $a): ?>
            <a href="/article/<?php echo htmlspecialchars($a['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="blog-card">
              <?php if (!empty($a['cover_image'])): ?>
              <div class="blog-cover"><img src="<?php echo htmlspecialchars($a['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($a['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" /></div>
              <?php endif; ?>
              <div class="blog-body">
                <h3><?php echo htmlspecialchars($a['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <?php if (!empty($a['excerpt_ne'])): ?>
                <p><?php echo htmlspecialchars($a['excerpt_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <strong class="blog-read">पूरा पढ्नुहोस् <?php echo renderIcon('ArrowRight'); ?></strong>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
          <div style="text-align: center; margin-top: 32px;">
            <a class="button button-maroon" href="/blog">सबै लेख हेर्नुहोस् <?php echo renderIcon('ArrowRight'); ?></a>
          </div>
        </div>
      </section>
<?php endif; ?>

<style>
.testimonial-section { background: #fff8ee; }
.testimonial-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
.testimonial { padding: 30px 26px; border-radius: 14px; background: white; border: 1px solid var(--line); box-shadow: var(--shadow); }
.testimonial svg { font-size: 36px; color: #dba65a; opacity: .5; }
.testimonial p { margin: 12px 0; font-size: 14px; line-height: 1.75; color: var(--muted); }
.testimonial strong { display: block; color: var(--ink); font-size: 15px; }
.testimonial span { display: block; color: var(--muted); font-size: 12px; margin-top: 4px; }
@media (max-width: 820px) { .testimonial-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 520px) { .testimonial-grid { grid-template-columns: 1fr; } }
</style>
<?php if (!empty($testimonials)): ?>
      <section class="section testimonial-section">
        <div class="container">
          <div class="section-heading"><span>सेवाग्राहीको प्रतिक्रिया</span><h2>हाम्रा सन्तुष्ट सेवाग्राही</h2></div>
          <div class="testimonial-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial">
              <?php echo renderIcon('Quotes'); ?>
              <p>“<?php echo htmlspecialchars($t['content'], ENT_QUOTES, 'UTF-8'); ?>”</p>
              <strong><?php echo htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
              <?php if (!empty($t['location'])): ?>
              <span><?php echo htmlspecialchars($t['location'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
<?php endif; ?>

<?php
renderPublicFooter(['/assets/js/home.js']);
