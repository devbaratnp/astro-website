<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('प्रारम्भिक जन्मकुण्डली | Astro Shree Hari', 'आफ्नो जन्म विवरणका आधारमा प्रारम्भिक ज्योतिषीय विवरण — राशि, नक्षत्र, लग्न र स्थान।', '/kundali', ['/assets/css/pages/forms.css']);
?>
<section class="section page-section">
  <div class="container feature-page">
    <div class="section-heading">
      <span>स्वचालित गणना</span>
      <h2>प्रारम्भिक जन्मकुण्डली</h2>
      <p>जन्म विवरण भर्नुहोस्। परिणाम प्रारम्भिक जानकारीका लागि मात्र हो।</p>
    </div>
    <form class="booking-form" id="kundali-form" action="#">
      <div class="form-grid">
        <label>नाम *<input name="name" required /></label>
        <label>फोन *<input name="phone" inputmode="tel" required /></label>
        <label>जन्म मिति *<input type="date" name="birth_date" required /></label>
        <label>जन्म समय *<input type="time" name="birth_time" required /></label>
        <label class="full">जन्म स्थान *<input name="birth_place" required /></label>
      </div>
      <button class="button button-maroon full-button">कुण्डली हेर्नुहोस्</button>
      <p class="form-error" id="kundali-error" style="display:none"></p>
    </form>
    <div class="result-grid" id="kundali-result" style="display:none">
      <article><b>राशि</b><strong id="result-rashi"></strong></article>
      <article><b>नक्षत्र</b><strong id="result-nakshatra"></strong></article>
      <article><b>लग्न</b><strong id="result-lagna"></strong></article>
      <article><b>स्थान</b><strong id="result-place"></strong></article>
      <div class="lagna-chart" id="lagna-chart"></div>
    </div>
  </div>
</section>
<?php renderPublicFooter(['/assets/js/kundali.js']); ?>
