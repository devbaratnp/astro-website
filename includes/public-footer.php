<?php

require_once __DIR__ . '/public-config.php';
require_once __DIR__ . '/public-icons.php';

/**
 * Render standard public page footer
 */
function renderPublicFooter(array $extraJs = []): void {
    ?>
      </main>

      <footer class="footer">
        <div class="container footer-grid">
          <div class="footer-brand-block">
            <a href="/" class="footer-brand" aria-label="श्रीहरि ज्योतिष गृहपृष्ठ">
              <img src="<?php echo assetUrl('/assets/shreehari-logo.webp'); ?>" alt="श्रीहरि ज्योतिष लोगो" width="64" height="64" />
              <span><strong>श्रीहरि पूजा भण्डार</strong><b>एवं ज्योतिष परामर्श केन्द्र नेपाल</b></span>
            </a>
            <p>शास्त्र, संस्कार र जीवनका लागि विश्वसनीय मार्गदर्शन। धर्मशास्त्र, कर्मकाण्ड र ज्योतिषशास्त्रमा आधारित सेवा।</p>
          </div>
          <div class="footer-column">
            <h3>द्रुत लिङ्कहरू</h3>
            <a href="/">गृहपृष्ठ</a>
            <a href="/about">हाम्रो बारेमा</a>
            <a href="/services">सेवाहरू</a>
            <a href="/blog">लेख तथा रचनाहरू</a>
            <a href="/events">कार्यक्रम तथा यात्रा</a>
            <a href="/gallery">मिडिया ग्यालेरी</a>
            <a href="/muhurta">मुहूर्त परीक्षण</a>
            <a href="/store">पूजा भण्डार</a>
            <a href="/appointment">परामर्श प्रक्रिया</a>
            <a href="/contact">सम्पर्क</a>
          </div>
          <div class="footer-column footer-contact">
            <h3>सम्पर्क जानकारी</h3>
            <a href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer">
              <?php echo renderIcon('WhatsappLogo'); ?> <span><?php echo PHONE_DISPLAY; ?></span>
            </a>
            <a href="mailto:<?php echo EMAIL; ?>">
              <?php echo renderIcon('EnvelopeSimple'); ?> <span><?php echo EMAIL; ?></span>
            </a>
            <span>
              <?php echo renderIcon('MapPin'); ?> <span>कमल–३, केर्खा, झापा, नेपाल</span>
            </span>
          </div>
          <div class="footer-column footer-social-column">
            <h3>हाम्रा सामाजिक सञ्जाल</h3>
            <div class="footer-social-links">
              <a href="https://youtube.com/@astrogurusitaram3m" target="_blank" rel="noreferrer" aria-label="YouTube">
                <?php echo renderIcon('YoutubeLogo'); ?> <span>YouTube</span>
              </a>
              <a href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noreferrer" aria-label="Facebook">
                <?php echo renderIcon('FacebookLogo'); ?> <span>Facebook</span>
              </a>
            </div>
          </div>
        </div>
        <div class="copyright">© २०२६ श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल । सर्वाधिकार सुरक्षित।</div>
      </footer>

      <a class="floating-whatsapp" href="https://wa.me/<?php echo PHONE; ?>" target="_blank" rel="noreferrer" aria-label="WhatsApp">
        <?php echo renderIcon('WhatsappLogo'); ?>
      </a>
    </div>

    <script src="<?php echo assetUrl('/assets/js/site.js'); ?>"></script>
    <?php foreach ($extraJs as $jsFile): ?>
      <script src="<?php echo htmlspecialchars(strpos($jsFile, '/') === 0 ? BASE_PATH . $jsFile : $jsFile, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
  </body>
</html>
    <?php
}
