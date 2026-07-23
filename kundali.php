<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('जन्मकुण्डली | Astro Shree Hari', 'सूर्य सिद्धान्तमा आधारित पूर्ण जन्मकुण्डली — ग्रह स्थिति, भाव, नवमांश र मांगलिक दोष।', '/kundali', ['/assets/css/pages/forms.css', '/assets/css/pages/kundali.css']);
?>
<section class="section page-section">
  <div class="container feature-page">
    <div class="section-heading">
      <span>सूर्य सिद्धान्त गणना</span>
      <h2>पूर्ण जन्मकुण्डली</h2>
      <p>जन्म विवरण भर्नुहोस्। शास्त्रसम्मत सूर्य सिद्धान्त ग्रह स्थिति, भाव, नवमांश र मांगलिक दोष सहित।</p>
    </div>

    <form class="booking-form" id="kundali-form" action="#">
      <div class="form-grid">
        <label>नाम *<input name="name" required /></label>
        <label>फोन *<input name="phone" inputmode="tel" required /></label>
        <label class="full">जन्म मिति (बि.सं.) *
          <span class="bs-date-row">
            <select name="birth_year" required>
              <option value="">वर्ष</option>
              <?php for ($y = 2055; $y <= 2085; $y++): ?>
              <option value="<?php echo $y; ?>"<?php echo $y === 2083 ? ' selected' : ''; ?>><?php echo $y; ?></option>
              <?php endfor; ?>
            </select>
            <select name="birth_month" required>
              <option value="">महिना</option>
              <option value="1">बैशाख</option><option value="2">जेठ</option><option value="3">असार</option>
              <option value="4" selected>श्रावण</option><option value="5">भाद्र</option><option value="6">आश्विन</option>
              <option value="7">कार्तिक</option><option value="8">मंसिर</option><option value="9">पौष</option>
              <option value="10">माघ</option><option value="11">फाल्गुन</option><option value="12">चैत्र</option>
            </select>
            <select name="birth_day" required>
              <option value="">गते</option>
              <?php for ($d = 1; $d <= 32; $d++): ?>
              <option value="<?php echo $d; ?>"<?php echo $d === 7 ? ' selected' : ''; ?>><?php echo $d; ?></option>
              <?php endfor; ?>
            </select>
          </span>
        </label>
        <label>जन्म समय *<input type="time" name="birth_time" required /></label>
        <label class="full">जन्म स्थान *<input name="birth_place" placeholder="जस्तै: काठमाडौँ" required /></label>
      </div>
      <button class="button button-maroon full-button">कुण्डली हेर्नुहोस्</button>
      <p class="form-error" id="kundali-error" style="display:none"></p>
    </form>

    <div id="kundali-result" style="display:none">
      <div class="section-heading result-heading">
        <span>जन्म विवरण</span>
        <h2 id="kundali-heading"></h2>
      </div>

      <div class="kundali-layout">
        <div class="kundali-chart-section">
          <h3 class="kundali-section-title">लग्न कुण्डली</h3>
          <div class="chart-container" id="kundali-chart"></div>
        </div>

        <div class="kundali-details-section">
          <div class="kundali-summary-cards">
            <div class="k-summary-card"><b>जन्म राशि</b><strong id="kr-rashi"></strong></div>
            <div class="k-summary-card"><b>जन्म नक्षत्र</b><strong id="kr-nakshatra"></strong></div>
            <div class="k-summary-card"><b>लग्न राशि</b><strong id="kr-lagna"></strong></div>
            <div class="k-summary-card"><b>नवमांश</b><strong id="kr-navamsha"></strong></div>
            <div class="k-summary-card"><b>नक्षत्र पाद</b><strong id="kr-pada"></strong></div>
            <div class="k-summary-card"><b>मांगलिक</b><strong id="kr-mangal"></strong></div>
          </div>

          <h3 class="kundali-section-title">ग्रह स्थिति</h3>
          <div class="graha-table-wrap">
            <table class="graha-table" id="graha-table">
              <thead><tr><th>ग्रह</th><th>राशि</th><th>अंश</th><th>भाव</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>

          <h3 class="kundali-section-title">भाव स्वामी</h3>
          <div class="bhava-grid" id="bhava-grid"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php renderPublicFooter(['/assets/js/kundali.js']); ?>