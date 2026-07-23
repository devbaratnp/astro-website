<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('कार्यक्रम तथा यात्रा | Astro Shree Hari', 'प्रवचन, अनुष्ठान तथा तीर्थयात्राको विस्तृत जानकारी — आगामी कार्यक्रम र धार्मिक भ्रमण।', '/events', ['/assets/css/pages/gallery.css']);

$db = getDbConnection();
$events = []; $tours = [];
if ($db) {
    $stmt = $db->query("SELECT * FROM events WHERE type='event' AND (date_from >= CURDATE() OR date_from IS NULL) ORDER BY date_from ASC LIMIT 20");
    $events = $stmt->fetchAll();
    $stmt = $db->query("SELECT * FROM events WHERE type='tour' AND (date_from >= CURDATE() OR date_from IS NULL) ORDER BY date_from ASC LIMIT 20");
    $tours = $stmt->fetchAll();
}
?>
<div class="section page-section">
  <div class="container">
    <div class="section-heading">
      <span>कार्यक्रम तथा यात्रा</span>
      <h2>आगामी कार्यक्रम र धार्मिक भ्रमण</h2>
      <p>प्रवचन, अनुष्ठान तथा तीर्थयात्राको विस्तृत जानकारी</p>
    </div>

    <div class="events-tabs">
      <button class="tab-btn active" data-tab="upcoming">प्रवचन तथा कार्यक्रम</button>
      <button class="tab-btn" data-tab="tour">धार्मिक यात्रा</button>
    </div>

    <div id="events-upcoming" class="events-grid tab-content">
      <?php foreach ($events as $e): ?>
        <article class="event-card">
          <?php if (!empty($e['cover_image'])): ?>
            <div class="event-cover"><img src="<?php echo htmlspecialchars($e['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($e['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" /></div>
          <?php endif; ?>
          <div class="event-body">
            <h3><?php echo htmlspecialchars($e['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <?php if (!empty($e['title_en'])): ?>
              <em class="event-title-en"><?php echo htmlspecialchars($e['title_en'], ENT_QUOTES, 'UTF-8'); ?></em>
            <?php endif; ?>
            <div class="event-details">
              <span><?php echo renderIcon('CalendarBlank'); ?> <?php echo $e['date_from'] ? substr($e['date_from'], 0, 10) : ''; ?><?php if (!empty($e['date_to'])): ?> — <?php echo substr($e['date_to'], 0, 10); ?><?php endif; ?></span>
              <?php if (!empty($e['time_from'])): ?>
                <span><?php echo renderIcon('Clock'); ?> <?php echo substr($e['time_from'], 0, 5); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['location'])): ?>
                <span><?php echo renderIcon('MapPin'); ?> <?php echo htmlspecialchars($e['location'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['contact_person'])): ?>
                <span><?php echo renderIcon('User'); ?> <?php echo htmlspecialchars($e['contact_person'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['contact_phone'])): ?>
                <span><?php echo renderIcon('Phone'); ?> <?php echo htmlspecialchars($e['contact_phone'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($e['description_ne'])): ?>
              <p><?php echo htmlspecialchars($e['description_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($e['registration_url'])): ?>
              <a href="<?php echo htmlspecialchars($e['registration_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" class="button button-maroon">दर्ता गर्नुहोस् <?php echo renderIcon('ArrowRight'); ?></a>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (empty($events)): ?>
        <p class="empty-text" style="grid-column:1/-1;text-align:center;padding:60px 0">हाल कुनै कार्यक्रम उपलब्ध छैन।</p>
      <?php endif; ?>
    </div>

    <div id="events-tour" class="events-grid tab-content" style="display:none">
      <?php foreach ($tours as $e): ?>
        <article class="event-card">
          <?php if (!empty($e['cover_image'])): ?>
            <div class="event-cover"><img src="<?php echo htmlspecialchars($e['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($e['title_ne'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" /></div>
          <?php endif; ?>
          <div class="event-body">
            <h3><?php echo htmlspecialchars($e['title_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <?php if (!empty($e['title_en'])): ?>
              <em class="event-title-en"><?php echo htmlspecialchars($e['title_en'], ENT_QUOTES, 'UTF-8'); ?></em>
            <?php endif; ?>
            <div class="event-details">
              <span><?php echo renderIcon('CalendarBlank'); ?> <?php echo $e['date_from'] ? substr($e['date_from'], 0, 10) : ''; ?><?php if (!empty($e['date_to'])): ?> — <?php echo substr($e['date_to'], 0, 10); ?><?php endif; ?></span>
              <?php if (!empty($e['time_from'])): ?>
                <span><?php echo renderIcon('Clock'); ?> <?php echo substr($e['time_from'], 0, 5); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['location'])): ?>
                <span><?php echo renderIcon('MapPin'); ?> <?php echo htmlspecialchars($e['location'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['contact_person'])): ?>
                <span><?php echo renderIcon('User'); ?> <?php echo htmlspecialchars($e['contact_person'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
              <?php if (!empty($e['contact_phone'])): ?>
                <span><?php echo renderIcon('Phone'); ?> <?php echo htmlspecialchars($e['contact_phone'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($e['description_ne'])): ?>
              <p><?php echo htmlspecialchars($e['description_ne'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($e['registration_url'])): ?>
              <a href="<?php echo htmlspecialchars($e['registration_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" class="button button-maroon">दर्ता गर्नुहोस् <?php echo renderIcon('ArrowRight'); ?></a>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (empty($tours)): ?>
        <p class="empty-text" style="grid-column:1/-1;text-align:center;padding:60px 0">हाल कुनै यात्रा उपलब्ध छैन।</p>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php renderPublicFooter(['/assets/js/events.js']); ?>
