<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('हाम्रा प्रमुख सेवाहरू | Astro Shree Hari', 'जीवनका हरेक पक्षका लागि वैदिक समाधान — जन्मकुण्डली, विवाह, वास्तु, ग्रह शान्ति, कर्मकाण्ड, मुहूर्त', '/services');
?>
<div class="section page-section">
  <div class="container" style="padding-top:40px">
    <div class="section-heading">
      <span>हाम्रा प्रमुख सेवाहरू</span>
      <h2>जीवनका हरेक पक्षका लागि वैदिक समाधान</h2>
      <p>शास्त्रसम्मत विधि, अनुभव र गोपनीयतामा आधारित व्यक्तिगत सेवा</p>
    </div>
    <div class="service-grid">
      <?php foreach (getServicesList() as $service): ?>
        <article class="service-card">
          <?php echo renderIcon($service['icon']); ?>
          <h3><?php echo $service['title']; ?></h3>
          <p><?php echo $service['text']; ?></p>
          <a href="/appointment">परामर्श लिनुहोस् <?php echo renderIcon('ArrowRight'); ?></a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php renderPublicFooter(); ?>
