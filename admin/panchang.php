<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/lib/Panchang.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_panchang'])) {
    $stmt = $db->prepare("INSERT INTO panchang (date, tithi, nakshatra, sunrise, sunset, rahu_kaal, auspicious_times, special_events_ne, special_events_en) VALUES (:date, :tithi, :nakshatra, :sunrise, :sunset, :rahu_kaal, :auspicious_times, :special_events_ne, :special_events_en) ON DUPLICATE KEY UPDATE tithi=:tithi2, nakshatra=:nakshatra2, sunrise=:sunrise2, sunset=:sunset2, rahu_kaal=:rahu_kaal2, auspicious_times=:auspicious_times2, special_events_ne=:special_events_ne2, special_events_en=:special_events_en2");
    $stmt->execute([
        ':date' => $_POST['date'],
        ':tithi' => sanitize($_POST['tithi']),
        ':nakshatra' => sanitize($_POST['nakshatra']),
        ':sunrise' => $_POST['sunrise'] ?: null,
        ':sunset' => $_POST['sunset'] ?: null,
        ':rahu_kaal' => $_POST['rahu_kaal'] ?: null,
        ':auspicious_times' => json_encode(array_filter(explode("\n", $_POST['auspicious_times'] ?? ''))),
        ':special_events_ne' => sanitize($_POST['special_events_ne'] ?? ''),
        ':special_events_en' => sanitize($_POST['special_events_en'] ?? ''),
        ':tithi2' => sanitize($_POST['tithi']),
        ':nakshatra2' => sanitize($_POST['nakshatra']),
        ':sunrise2' => $_POST['sunrise'] ?: null,
        ':sunset2' => $_POST['sunset'] ?: null,
        ':rahu_kaal2' => $_POST['rahu_kaal'] ?: null,
        ':auspicious_times2' => json_encode(array_filter(explode("\n", $_POST['auspicious_times'] ?? ''))),
        ':special_events_ne2' => sanitize($_POST['special_events_ne'] ?? ''),
        ':special_events_en2' => sanitize($_POST['special_events_en'] ?? ''),
    ]);
    echo '<div class="alert alert-success">पञ्चाङ्ग सुरक्षित गरियो</div>';
}

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$stmt = $db->prepare("SELECT * FROM panchang WHERE date = :date");
$stmt->execute([':date' => $selectedDate]);
$panchang = $stmt->fetch();

// Auto-calculate from library if no manual entry
$calculated = Panchang::getForDate($selectedDate);

$recent = $db->query("SELECT date, tithi, nakshatra FROM panchang ORDER BY date DESC LIMIT 20")->fetchAll();
?>
<h1>पञ्चाङ्ग</h1>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
  <div class="form-card">
    <h3>पञ्चाङ्ग सम्पादन गर्नुहोस्</h3>
    <p style="color:var(--muted);font-size:.85rem;margin:0 0 16px">
      स्वचालित गणना: <strong><?= $calculated['tithi'] ?></strong>, <?= $calculated['nakshatra'] ?>,
      सूर्योदय <?= $calculated['sunrise'] ?>/<?= $calculated['sunset'] ?>
    </p>
    <form method="POST">
      <div class="form-grid">
        <div class="field"><label>मिति</label><input name="date" type="date" value="<?= $selectedDate ?>" onchange="this.form.submit()" style="cursor:pointer;background:var(--cream)"></div>
        <div class="field"><label>तिथि</label><input name="tithi" value="<?= htmlspecialchars($panchang['tithi'] ?? $calculated['tithi']) ?>"></div>
        <div class="field"><label>नक्षत्र</label><input name="nakshatra" value="<?= htmlspecialchars($panchang['nakshatra'] ?? $calculated['nakshatra']) ?>"></div>
        <div class="field"><label>सूर्योदय</label><input name="sunrise" type="time" value="<?= htmlspecialchars($panchang['sunrise'] ?? $calculated['sunrise']) ?>"></div>
        <div class="field"><label>सूर्यास्त</label><input name="sunset" type="time" value="<?= htmlspecialchars($panchang['sunset'] ?? $calculated['sunset']) ?>"></div>
        <div class="field"><label>राहुकाल</label><input name="rahu_kaal" type="time" value="<?= htmlspecialchars($panchang['rahu_kaal'] ?? '') ?>"></div>
        <div class="field full"><label>शुभ समय (प्रति लाइन एक)</label>
          <textarea name="auspicious_times" rows="3"><?php
            $times = $panchang ? json_decode($panchang['auspicious_times'] ?? '[]', true) : [];
            echo htmlspecialchars(implode("\n", $times));
          ?></textarea>
        </div>
        <div class="field"><label>विशेष (नेपाली)</label><input name="special_events_ne" value="<?= htmlspecialchars($panchang['special_events_ne'] ?? '') ?>"></div>
        <div class="field"><label>विशेष (अङ्ग्रेजी)</label><input name="special_events_en" value="<?= htmlspecialchars($panchang['special_events_en'] ?? '') ?>"></div>
      </div>
      <button type="submit" name="save_panchang" class="btn btn-primary" style="margin-top:16px">पञ्चाङ्ग सुरक्षित गर्नुहोस्</button>
    </form>
  </div>

  <div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr><th>मिति</th><th>तिथि</th><th>नक्षत्र</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $r): ?>
          <tr>
            <td><a href="?date=<?= $r['date'] ?>" style="color:var(--deep-saffron);font-weight:600"><?= $r['date'] ?></a></td>
            <td><?= htmlspecialchars($r['tithi']) ?></td>
            <td><?= htmlspecialchars($r['nakshatra']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p style="color:var(--muted);font-size:.85rem;margin-top:12px">
      • पञ्चाङ्ग स्वचालित रूपमा गणना हुन्छ<br>
      • आवश्यक परेमा माथिको फारमबाट म्यानुअल ओभरराइड गर्न सकिन्छ
    </p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
