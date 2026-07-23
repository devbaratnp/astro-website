<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('मुहूर्त परीक्षण | Astro Shree Hari', 'विवाह, गृहप्रवेश, व्रतबन्ध, व्यवसाय र यात्राका लागि शुभ समय जाँच गर्नुहोस्।', '/muhurta', ['/assets/css/pages/forms.css']);
?>
<section class="section page-section">
  <div class="container feature-page">
    <div class="section-heading">
      <span>शुभ मुहूर्त</span>
      <h2>मुहूर्त परीक्षण</h2>
      <p>विवाह, गृहप्रवेश, व्रतबन्ध, व्यवसाय र यात्राका लागि शुभ समय जाँच गर्नुहोस्</p>
    </div>
    <form class="booking-form" id="muhurta-form" action="#">
      <div class="form-grid">
        <label class="full">कार्य चयन
          <select id="muhurta-type">
            <option value="विवाह">विवाह</option>
            <option value="गृहप्रवेश">गृहप्रवेश</option>
            <option value="व्रतबन्ध">व्रतबन्ध</option>
            <option value="व्यवसाय">व्यवसाय</option>
            <option value="यात्रा">यात्रा</option>
          </select>
        </label>
        <div class="section-divider full"><span>मिति (नेपाली)</span></div>
        <div class="bs-date-row full">
          <div class="bs-date-field">
            <small>वर्ष</small>
            <select id="bs-year"></select>
          </div>
          <div class="bs-date-field">
            <small>महिना</small>
            <select id="bs-month"></select>
          </div>
          <div class="bs-date-field">
            <small>गते</small>
            <select id="bs-day"></select>
          </div>
        </div>
      </div>
      <button class="button button-maroon full-button">मुहूर्त हेर्नुहोस्</button>
    </form>
    <div id="muhurta-result" style="display:none;margin-top:32px">
      <div class="result-grid" style="grid-template-columns:1fr">
        <article class="verdict-head" id="verdict-head" style="display:flex;align-items:center;gap:16px;padding:28px">
          <div id="verdict-icon"></div>
          <div>
            <strong id="verdict-text" style="font-size:22px"></strong>
            <p id="verdict-desc" style="margin:6px 0 0;color:var(--muted);font-size:14px"></p>
          </div>
        </article>
      </div>
      <div class="result-grid" id="verdict-details">
        <article><b>मिति</b><strong id="detail-date"></strong></article>
        <article><b>वार</b><strong id="detail-day"></strong></article>
        <article><b>नक्षत्र</b><strong id="detail-nakshatra"></strong></article>
        <article><b>तिथि</b><strong id="detail-tithi"></strong></article>
        <article><b>चन्द्र राशि</b><strong id="detail-moon-rashi"></strong></article>
        <article><b>कार्य</b><strong id="detail-type"></strong></article>
      </div>
      <p style="margin-top:16px;font-size:12px;color:var(--muted);text-align:center">यो सामान्य मुहूर्त जानकारी हो; व्यक्तिगत कुण्डली अनुसार विस्तृत परामर्शका लागि गुरुज्यूसँग सम्पर्क गर्नुहोस्।</p>
    </div>
  </div>
</section>
<?php renderPublicFooter(['/assets/js/muhurta.js']); ?>
