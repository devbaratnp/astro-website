<?php

require_once __DIR__ . '/includes/public-config.php';

$formSuccess = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['_csrf'] ?? '';
    if (!hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        $formError = 'CSRF token mismatch — कृपया पृष्ठ रिफ्रेस गरी पुन: प्रयास गर्नुहोस्।';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        if (!$name || !$subject || !$message) {
            $formError = 'कृपया सबै आवश्यक फिल्डहरू भर्नुहोस्।';
        } elseif (mb_strlen($name) > 100 || mb_strlen($subject) > 200) {
            $formError = 'इनपुट अधिकतम लम्बाइ भन्दा बढी छ।';
        } else {
            try {
                $db = getDbConnection();
                if ($db) {
                    $stmt = $db->prepare("INSERT INTO contact_messages (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $phone, $email, $subject, $message]);
                    $formSuccess = 'तपाईंको सन्देश प्राप्त भयो। हामी चाँडै सम्पर्क गर्नेछौं।';
                } else {
                    $formError = 'डेटाबेस जडानमा समस्या भयो। कृपया पछि पुन: प्रयास गर्नुहोस्।';
                }
            } catch (Throwable $e) {
                $formError = 'सन्देश पठाउन सकिएन। कृपया पछि पुन: प्रयास गर्नुहोस्।';
                error_log('Contact form error: ' . $e->getMessage());
            }
        }
    }
}

renderPublicHeader(
    'सम्पर्क | Astro Shree Hari',
    'हामीसँग जोडिनुहोस् — फोन, इमेल, WhatsApp र प्रत्यक्ष कार्यालय मार्फत सम्पर्क गर्नुहोस्।',
    '/contact',
    ['/assets/css/pages/forms.css']
);

?>

<section class="section page-section">
  <div class="container" style="padding-top:40px">
    <div class="section-heading">
      <span>सम्पर्क</span>
      <h2>हामीसँग जोडिनुहोस्</h2>
      <p>तपाईंको प्रश्न, सुझाव वा परामर्शका लागि हामीलाई सम्पर्क गर्नुहोस्।</p>
    </div>

    <div class="contact-grid" style="margin-top:30px">
      <div class="contact-card">
        <h3>सम्पर्क विवरण</h3>

        <a href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer">
          <?php echo renderIcon('WhatsappLogo', '', 'weight="fill"'); ?><span>WhatsApp<strong>+977 9844639228</strong></span>
        </a>

        <a href="tel:+<?php echo PHONE; ?>">
          <?php echo renderIcon('Phone'); ?><span>फोन<strong>+977 9844639228</strong></span>
        </a>

        <a href="mailto:<?php echo EMAIL; ?>">
          <?php echo renderIcon('EnvelopeSimple'); ?><span>इमेल<strong><?php echo EMAIL; ?></strong></span>
        </a>

        <div>
          <?php echo renderIcon('MapPin'); ?><span>प्रधान कार्यालय<strong>कमल-३, केर्खा, झापा, नेपाल</strong></span>
        </div>

        <div>
          <?php echo renderIcon('Clock'); ?><span>परामर्श माध्यम<strong>प्रत्यक्ष तथा अनलाइन (WhatsApp/Video)</strong></span>
        </div>

        <div class="socials" style="margin-top:16px">
          <a href="https://youtube.com/@astrogurusitaram3m" target="_blank" rel="noreferrer" aria-label="YouTube"><?php echo renderIcon('YoutubeLogo'); ?></a>
          <a href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noreferrer" aria-label="Facebook"><?php echo renderIcon('FacebookLogo'); ?></a>
          <a href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer" aria-label="WhatsApp"><?php echo renderIcon('WhatsappLogo'); ?></a>
        </div>
      </div>

      <div class="contact-map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3563.048259485282!2d87.68542367532926!3d26.738298676745547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sne!2snp!4v1722334567890" width="100%" height="250" style="border:0;border-radius:10px" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="श्रीहरि ज्योतिष परामर्श केन्द्र — झापा"></iframe>
      </div>

      <form class="booking-form" id="contact-form" method="post">
        <?php echo csrfField(); ?>
        <div class="form-title"><span>सन्देश</span><h3>हामीलाई लेख्नुहोस्</h3></div>
        <div class="form-grid">
          <label>नाम *<input name="name" required maxlength="100" /></label>
          <label>फोन<input name="phone" inputmode="tel" maxlength="20" /></label>
          <label class="full">इमेल<input name="email" type="email" maxlength="100" /></label>
          <label class="full">विषय *<input name="subject" required maxlength="200" /></label>
          <label class="full">सन्देश *<textarea name="message" required rows="5"></textarea></label>
        </div>
        <button type="submit" class="button button-maroon full-button" id="contact-submit">सन्देश पठाउनुहोस्</button>
        <?php if ($formSuccess): ?>
        <p class="success"><?php echo htmlspecialchars($formSuccess, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif ($formError): ?>
        <p class="form-error"><?php echo htmlspecialchars($formError, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
      </form>
    </div>
  </div>
</section>

<?php renderPublicFooter(['/assets/js/contact.js']); ?>
