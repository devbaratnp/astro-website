<?php
require_once __DIR__ . '/includes/public-header.php';

function n($num) {
    $nepali = ['०','१','२','३','४','५','६','७','८','९'];
    return str_replace(range(0, 9), $nepali, (string)$num);
}

renderPublicHeader(
    'परामर्श बुक गर्नुहोस् | Astro Shree Hari',
    'आफ्नो समय बुक गरी शास्त्रसम्मत मार्गदर्शन लिनुहोस्। प्रत्यक्ष वा अनलाइन परामर्श।',
    '/appointment',
    ['/assets/css/pages/forms.css']
);
?>
<section class="section page-section">
  <div class="container contact-grid">
    <div class="contact-card">
      <span class="section-kicker">आजै सम्पर्क गर्नुहोस्</span>
      <h2>परामर्श बुक गर्नुहोस्</h2>
      <p>आफ्नो समय बुक गरी शास्त्रसम्मत मार्गदर्शन लिनुहोस्।</p>
      <a href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer">
        <?php echo renderIcon('WhatsappLogo'); ?><span>WhatsApp<strong>+977 9844639228</strong></span>
      </a>
      <a href="mailto:<?php echo EMAIL; ?>">
        <?php echo renderIcon('EnvelopeSimple'); ?><span>इमेल<strong><?php echo EMAIL; ?></strong></span>
      </a>
      <div>
        <?php echo renderIcon('Clock'); ?><span>परामर्श माध्यम<strong>प्रत्यक्ष तथा अनलाइन</strong></span>
      </div>
    </div>

    <form class="booking-form" id="appointment-form">
      <div class="form-title"><span>शुभ लाभ</span><h3>आफ्नो विवरण पठाउनुहोस्</h3></div>
      <div class="form-grid">
        <label>तपाईंको नाम *<input name="name" required placeholder="पूरा नाम लेख्नुहोस्" /></label>
        <label>फोन नम्बर *<input name="phone" required inputmode="tel" placeholder="98XXXXXXXX" /></label>

        <div class="full section-divider"><span>जन्म विवरण</span></div>

        <label class="full">जन्म मिति (वि.सं.) *
          <div class="bs-date-row">
            <span class="bs-date-field">
              <small>वर्ष</small>
              <select id="birth-bs-year" required>
                <?php for ($y = 1970; $y <= 2090; $y++): ?>
                <option value="<?php echo $y; ?>"><?php echo n($y); ?></option>
                <?php endfor; ?>
              </select>
            </span>
            <span class="bs-date-field">
              <small>महिना</small>
              <select id="birth-bs-month" required>
                <option value="1">बैशाख</option>
                <option value="2">जेठ</option>
                <option value="3">असार</option>
                <option value="4">श्रावण</option>
                <option value="5">भाद्र</option>
                <option value="6">आश्विन</option>
                <option value="7">कार्तिक</option>
                <option value="8">मंसिर</option>
                <option value="9">पौष</option>
                <option value="10">माघ</option>
                <option value="11">फाल्गुन</option>
                <option value="12">चैत्र</option>
              </select>
            </span>
            <span class="bs-date-field">
              <small>गते</small>
              <select id="birth-bs-day" required>
              </select>
            </span>
          </div>
        </label>

        <label class="full">जन्म समय *
          <div class="bs-time-row">
            <button type="button" class="am-pm-btn active" id="am-btn">बिहान (AM)</button>
            <button type="button" class="am-pm-btn" id="pm-btn">बेलुका (PM)</button>
            <input type="hidden" name="birth_am_pm" id="birth-am-pm" value="am" />
            <span class="bs-date-field" style="flex:0 0 90px">
              <small>घण्टा</small>
              <select id="birth-hour" required>
                <?php for ($h = 1; $h <= 12; $h++): ?>
                <option value="<?php echo $h; ?>"><?php echo n($h); ?></option>
                <?php endfor; ?>
              </select>
            </span>
            <span style="padding:0 4px;margin-top:24px">:</span>
            <span class="bs-date-field" style="flex:0 0 90px">
              <small>मिनेट</small>
              <select id="birth-minute" required>
                <?php for ($m = 0; $m < 60; $m++): ?>
                <option value="<?php echo $m; ?>"><?php echo n(str_pad($m, 2, '0', STR_PAD_LEFT)); ?></option>
                <?php endfor; ?>
              </select>
            </span>
          </div>
        </label>

        <input type="hidden" name="birth_date" id="birth-date" />
        <input type="hidden" name="birth_time" id="birth-time" />

        <label class="full">जन्मस्थान *
          <input name="birth_place" required placeholder="गाउँ / शहर / जिल्ला" />
        </label>

        <label>नाउँरणको नाम *
          <input name="nwaran_name" required placeholder="नाउँरण (न्वारण) नाम" />
        </label>
        <label>पिताको नाम *
          <input name="father_name" required placeholder="पिताको पूरा नाम" />
        </label>
        <label>माताको नाम *
          <input name="mother_name" required placeholder="माताको पूरा नाम" />
        </label>

        <label class="full">सन्तान क्रम *
          <div class="bs-order-row">
            <span class="bs-date-field">
              <small>क्रम</small>
              <select name="birth_order" required>
                <option value="">-- क्रम --</option>
                <option value="पहिलो">पहिलो</option>
                <option value="दोस्रो">दोस्रो</option>
                <option value="तेस्रो">तेस्रो</option>
                <option value="चौथो">चौथो</option>
                <option value="पाँचौं">पाँचौं</option>
              </select>
            </span>
            <span class="bs-date-field">
              <small>लिङ्ग</small>
              <select name="birth_gender" required>
                <option value="">-- लिङ्ग --</option>
                <option value="छोरा">छोरा (Son)</option>
                <option value="छोरी">छोरी (Daughter)</option>
              </select>
            </span>
          </div>
        </label>

        <label>सेवा छान्नुहोस् *<select name="service" required><option value="" disabled selected>सेवा छान्नुहोस्</option>
          <option value="kundali">जन्मकुण्डली विश्लेषण</option>
          <option value="marriage">विवाह तथा गुण मिलान</option>
          <option value="grahadasha">ग्रह शान्ति</option>
          <option value="vastu">वास्तु परामर्श</option>
          <option value="pooja">वैदिक कर्मकाण्ड</option>
          <option value="general">शुभ मुहूर्त</option>
        </select></label>
        <label>इमेल<input name="email" type="email" placeholder="तपाईंको इमेल" /></label>
        <label>मिति *<input name="preferred_date" type="date" id="preferred-date" required /></label>
        <span id="slot-loading" class="slot-loading" style="display:none">समय सूची लोड हुँदैछ…</span>
        <label>समय *<select name="preferred_time" id="preferred-time" required><option value="" disabled selected>समय छान्नुहोस्</option></select></label>
        <label>परामर्श माध्यम<select name="consultation_mode">
          <option value="whatsapp">WhatsApp</option>
          <option value="video">Video consultation</option>
          <option value="phone">Phone</option>
          <option value="inperson">प्रत्यक्ष</option>
        </select></label>
        <label class="full">तपाईंको प्रश्न / समस्या *<textarea name="message" required rows="4" placeholder="आफ्नो प्रश्न वा समस्या विस्तारमा लेख्नुहोस्..."></textarea></label>
      </div>
      <button class="button button-maroon full-button" type="submit" id="submit-btn">WhatsApp मार्फत अनुरोध पठाउनुहोस्</button>
      <p id="success-msg" class="success" style="display:none"></p>
      <p id="meeting-url" class="success" style="display:none"></p>
      <p id="error-msg" class="form-error" style="display:none"></p>
      <small><?php echo renderIcon('LockKey'); ?> तपाईंको जानकारी पूर्ण गोपनीय र सुरक्षित रहनेछ।</small>
    </form>
  </div>
</section>
<?php renderPublicFooter(['/assets/js/appointment.js']); ?>
