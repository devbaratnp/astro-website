<?php
require_once __DIR__ . '/includes/public-header.php';

renderPublicHeader(
    'भुक्तानी पुष्टि | Astro Shree Hari',
    'Static QR भुक्तानी पुष्टि गर्नुहोस् — Transaction ID र स्क्रिनसट पठाउनुहोस्।',
    '/payment',
    ['/assets/css/pages/forms.css']
);
?>
<section class="section page-section">
  <div class="container payment-layout">
    <div class="payment-qr">
      <h2>Static QR Payment</h2>
      <div class="qr-placeholder">QR</div>
      <p>वास्तविक QR र खाताको विवरण client बाट प्राप्त भएपछि यहाँ राखिनेछ। अहिले रकम नपठाउनुहोस्।</p>
    </div>
    <form class="booking-form" id="payment-form">
      <div class="form-title"><span>शुभ लाभ</span><h3>भुक्तानी विवरण पठाउनुहोस्</h3></div>
      <div class="form-grid">
        <label>बुकिङ प्रकार
          <select name="booking_type" required>
            <option value="appointment">Appointment</option>
            <option value="pooja">Pooja</option>
          </select>
        </label>
        <label>बुकिङ नं.
          <input type="number" min="1" name="booking_id" required />
        </label>
        <label>नाम
          <input name="user_name" required />
        </label>
        <label>फोन
          <input name="user_phone" required />
        </label>
        <label>रकम
          <input type="number" min="1" step="0.01" name="amount" required />
        </label>
        <label>विधि
          <select name="method" required>
            <option value="bank">Static QR / Bank</option>
            <option value="esewa">eSewa QR</option>
            <option value="khalti">Khalti QR</option>
            <option value="imepay">IME Pay QR</option>
          </select>
        </label>
        <label class="full">Transaction ID
          <input name="transaction_ref" required />
        </label>
        <label class="full">Screenshot
          <input type="file" name="screenshot" accept="image/jpeg,image/png,image/webp" />
        </label>
      </div>
      <button class="button button-maroon full-button" type="submit" id="submit-btn">Verification का लागि पठाउनुहोस्</button>
      <p id="success-msg" class="success" style="display:none"></p>
      <p id="error-msg" class="form-error" style="display:none"></p>
    </form>
  </div>
</section>
<?php renderPublicFooter(['/assets/js/payment.js']); ?>
