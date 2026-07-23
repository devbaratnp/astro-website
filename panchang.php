<?php
require_once __DIR__ . '/includes/public-header.php';

$date = $_GET['date'] ?? date('Y-m-d');

$panchang = null;
$horoscope = ['items' => []];

try {
    ob_start();
    $_GET['date'] = $date;
    require __DIR__ . '/backend/api/panchang.php';
    $output = ob_get_clean();
    $data = json_decode($output, true);
    if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
        $panchang = $data['data']['panchang'] ?? null;
    }
} catch (Throwable $e) {
    ob_end_clean();
    error_log('panchang page error: ' . $e->getMessage());
}

try {
    ob_start();
    $_GET['date'] = $date;
    require __DIR__ . '/backend/api/horoscope.php';
    $output = ob_get_clean();
    $data = json_decode($output, true);
    if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
        $horoscope = $data['data'];
    }
} catch (Throwable $e) {
    ob_end_clean();
    error_log('horoscope page error: ' . $e->getMessage());
}

$fmt = new IntlDateFormatter('ne-NP', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Kathmandu');
$dateStr = $fmt->format(strtotime($date));
$dayNum = (int) date('j', strtotime($date));

$tabs = ['पञ्चाङ्ग', 'दिन फल', 'रात्री फल'];

$panchangRows = [];
if ($panchang) {
    $labels = ['तिथि' => 'tithi', 'सूर्योदय' => 'sunrise', 'सूर्यास्त' => 'sunset', 'नक्षत्र' => 'nakshatra', 'करण' => 'karana', 'योग' => 'yoga'];
    foreach ($labels as $label => $key) {
        if (!empty($panchang[$key])) {
            $panchangRows[] = [$label, $panchang[$key]];
        }
    }
}

$items = $horoscope['items'] ?? [];

renderPublicHeader('आजको पञ्चाङ्ग र राशिफल | Astro Shree Hari', 'आजको तिथि, नक्षत्र, सूर्योदय, सूर्यास्त र दैनिक राशिफल हेर्नुहोस्। शास्त्रसम्मत पञ्चाङ्ग विवरण।', '/panchang', ['/assets/css/pages/panchang.css']);
?>
<section class="section page-section panchang-page">
  <div class="container panchang-shell">
    <div class="panchang-date-nav">
      <button type="button" class="nav-btn" id="prev-day" aria-label="अघिल्लो दिन">&lsaquo;</button>
      <label>
        आजको पञ्चाङ्ग
        <input type="date" id="panchang-date" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>" />
      </label>
      <button type="button" class="nav-btn" id="next-day" aria-label="अर्को दिन">&rsaquo;</button>
    </div>

    <header class="panchang-hero">
      <aside class="panchang-calendar">
        <strong><?php echo $dayNum; ?></strong>
        <b><?php echo htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8'); ?></b>
        <span>आजको पञ्चाङ्ग</span>
        <i aria-hidden="true"></i>
      </aside>
      <div>
        <span class="panchang-kicker">दैनिक अपडेट</span>
        <h1><?php echo htmlspecialchars(($panchang['special_events_ne'] ?? 'आजको पञ्चाङ्ग') ?: 'आजको पञ्चाङ्ग', ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="panchang-summary">
          <div>तिथि<strong id="summary-tithi"><?php echo htmlspecialchars($panchang['tithi'] ?? 'लोड हुँदैछ…', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>नक्षत्र<strong id="summary-nakshatra"><?php echo htmlspecialchars($panchang['nakshatra'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>सूर्योदय<strong id="summary-sunrise"><?php echo htmlspecialchars($panchang['sunrise'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>सूर्यास्त<strong id="summary-sunset"><?php echo htmlspecialchars($panchang['sunset'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      </div>
    </header>

    <div class="panchang-tabs" role="tablist">
      <?php foreach ($tabs as $tab): ?>
        <button type="button" role="tab" class="tab-btn <?php echo $tab === 'पञ्चाङ्ग' ? 'active' : ''; ?>" data-tab="<?php echo htmlspecialchars($tab, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tab, ENT_QUOTES, 'UTF-8'); ?></button>
      <?php endforeach; ?>
    </div>

    <section class="panchang-detail-card" id="panchang-detail">
      <h2 id="detail-title">पञ्चाङ्ग</h2>
      <hr />

      <div class="tab-content" id="tab-panchang" style="display:block">
        <?php if (count($panchangRows) > 0): ?>
          <div class="panchang-facts">
            <?php foreach ($panchangRows as $row): ?>
              <div><b><?php echo htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8'); ?></b><span><?php echo htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="panchang-placeholder">पञ्चाङ्ग विवरण अहिले उपलब्ध छैन।</p>
        <?php endif; ?>
      </div>

      <div class="tab-content" id="tab-day" style="display:none">
        <div class="panchang-forecast-grid" id="day-forecast-grid">
          <?php
          $dayEntries = array_filter($items, function($it) { return !empty($it['moon_interpretation']); });
          foreach ($dayEntries as $it):
          ?>
            <article class="panchang-forecast-card">
              <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p><?php echo htmlspecialchars($it['moon_interpretation'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
          <?php endforeach; ?>
          <?php if (count($dayEntries) === 0): ?>
            <p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="tab-content" id="tab-night" style="display:none">
        <div class="panchang-forecast-grid" id="night-forecast-grid">
          <?php
          $nightEntries = array_filter($items, function($it) { return !empty($it['remedy_tips']); });
          foreach ($nightEntries as $it):
          ?>
            <article class="panchang-forecast-card">
              <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p><?php echo htmlspecialchars($it['remedy_tips'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php if (!empty($it['infeasible_transit_moon'])): ?>
                <small><?php echo htmlspecialchars($it['infeasible_transit_moon'], ENT_QUOTES, 'UTF-8'); ?></small>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
          <?php if (count($nightEntries) === 0): ?>
            <p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <p class="panchang-notice">विवरण प्रशासनिक र गणनात्मक डाटामा आधारित छ।</p>

    <h2 class="subheading">आजको राशिफल</h2>
    <div class="horoscope-grid" id="horoscope-grid">
      <?php foreach ($items as $it): ?>
        <article class="horoscope-card">
          <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <p><?php echo htmlspecialchars($it['moon_interpretation'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script id="panchang-data" type="application/json">
<?php echo json_encode([
    'panchang' => $panchang,
    'horoscope' => $horoscope,
], JSON_UNESCAPED_UNICODE); ?>
</script>
<?php
renderPublicFooter(['/assets/js/panchang.js']);
