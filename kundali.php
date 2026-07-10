<!doctype html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>जन्मकुण्डली | श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल</title>
    <meta name="description" content="आफ्नो जन्म मिति, समय र स्थान प्रविष्ट गरी आधारभूत कुण्डली, राशि र नक्षत्र हेर्नुहोस्।">
    <meta name="theme-color" content="#711d29">
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="topbar">
        <div class="container">
            <div class="topbar-links"><span>ॐ श्री गणेशाय नमः</span><span>ज्योतिष तथा कर्मकाण्डमा १८+ वर्षको अनुभव</span></div>
            <div class="topbar-links"><a href="tel:+9779844639228">☎ +977 9844639228</a><a href="tel:+9779818234776">☎ +977 9818234776</a><a href="mailto:shreeharijyotishparamarsakendr@gmail.com">✉ shreeharijyotishparamarsakendr@gmail.com</a></div>
        </div>
    </div>
    <header class="site-header">
        <nav class="navbar container" aria-label="मुख्य नेभिगेसन">
            <a class="brand" href="index.php">
                <img src="assets/logo.svg" alt="श्रीहरि ज्योतिष परामर्श केन्द्र लोगो">
                <span><strong>श्रीहरि पूजा भण्डार</strong><small>Sitaram Timalsena · Nepali Astrologer</small></span>
            </a>
            <div class="nav-links"><a href="index.php">गृहपृष्ठ</a><a href="about.php">हाम्रो बारेमा</a><a href="services.php">सेवाहरू</a><a href="kundali.php" class="active">कुण्डली</a><a href="appointment.php">परामर्श</a><a href="contact.php">सम्पर्क</a></div>
            <div class="nav-actions">
                <a class="btn btn-primary" href="appointment.php">परामर्श बुक गर्नुहोस्</a>
                <button class="menu-toggle" aria-label="मेनु खोल्नुहोस्" aria-expanded="false">☰</button>
            </div>
        </nav>
    </header>
    <main>
        <section class="page-hero">
            <div class="container">
                <div class="eyebrow">जन्मकुण्डली</div>
                <h1>आफ्नो कुण्डली हेर्नुहोस्</h1>
                <div class="breadcrumb"><a href="index.php">गृहपृष्ठ</a><span>›</span><span>कुण्डली</span></div>
            </div>
        </section>
        <section class="section">
            <div class="container contact-grid">
                <div class="reveal">
                    <div class="eyebrow">आधारभूत विवरण</div>
                    <h2 class="content-title">जन्म मिति, समय र स्थान प्रविष्ट गर्नुहोस्</h2>
                    <p>तलको फाराम भर्नुहोस् र तपाईंको राशि, नक्षत्र र लग्न स्वचालित रूपमा गणना गरिनेछ।</p>
                    <ul class="check-list">
                        <li>जन्म मिति र समय सही भए अध्ययन अझ उपयोगी हुन्छ</li>
                        <li>समय थाहा नभए मिति मात्रै हाल्नुहोस्</li>
                        <li>विस्तृत कुण्डली विश्लेषणका लागि परामर्श लिनुहोस्</li>
                    </ul>
                </div>
                <div class="form-card reveal">
                    <h2 style="margin-top:0;color:var(--maroon)">कुण्डली फाराम</h2>
                    <form id="kundaliForm">
                        <div class="form-grid">
                            <div class="field"><label>पूरा नाम *</label><input name="name" id="kName" required placeholder="तपाईंको पूरा नाम"></div>
                            <div class="field"><label>फोन (वैकल्पिक)</label><input name="phone" id="kPhone" inputmode="tel" placeholder="+977..."></div>
                            <div class="field"><label>जन्म मिति *</label><input name="birth_date" id="kDate" type="date" required></div>
                            <div class="field"><label>जन्म समय</label><input name="birth_time" id="kTime" type="time"></div>
                            <div class="field full"><label>जन्म स्थान</label><input name="birth_place" id="kPlace" placeholder="गाउँ/सहर, जिल्ला, देश"></div>
                        </div>
                        <button class="btn btn-primary" type="submit">कुण्डली हेर्नुहोस्</button>
                    </form>
                    <div class="form-status" id="kundaliStatus" role="status" style="display:none"></div>
                </div>
            </div>
        </section>
        <section class="section-sm" id="kundaliResult" style="display:none">
            <div class="container">
                <div class="section-header reveal">
                    <div class="eyebrow">तपाईंको कुण्डली</div>
                    <h2>आधारभूत विवरण</h2>
                </div>
                <div class="grid grid-3" id="kundaliCards"></div>
                <div class="cta-box reveal" style="margin-top:30px">
                    <div>
                        <h2>विस्तृत विश्लेषण चाहिन्छ?</h2>
                        <p>पूरा कुण्डली, ग्रहदशा र व्यक्तिगत मार्गदर्शनका लागि परामर्श लिनुहोस्।</p>
                    </div>
                    <a class="btn btn-whatsapp" href="appointment.php">परामर्श बुक गर्नुहोस्</a>
                </div>
            </div>
        </section>
    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a class="brand" href="index.php"><img src="assets/logo.svg" alt=""><span><strong style="color:#f4d782">श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल</strong><small style="color:#d5bdb5">परम्परा, अनुभव र व्यक्तिगत मार्गदर्शन</small></span></a>
                    <p>ज्योतिष तथा कर्मकाण्ड विषयमा १८ वर्षभन्दा बढी अनुभवसहित नेपाल तथा विश्वभर अनलाइन परामर्श।</p>
                    <p style="font-size:0.9rem;color:#d5bdb5">South Asian Astro Federation · Nepal Jyotish Parishad · Wihswa Jyotish Mahasangh Certified</p>
                    <div class="social-links"><a class="social-link" href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noopener" aria-label="Facebook">f</a><a class="social-link" href="https://youtube.com/@astrogurusitaram3m?si=x37KRR6Wv4PldyRq" target="_blank" rel="noopener" aria-label="YouTube">▶</a></div>
                </div>
                <div><h3 class="footer-title">द्रुत लिंक</h3><div class="footer-links"><a href="about.php">हाम्रो बारेमा</a><a href="services.php">सेवाहरू</a><a href="kundali.php">कुण्डली</a><a href="appointment.php">परामर्श बुकिङ</a><a href="contact.php">सम्पर्क</a></div></div>
                <div><h3 class="footer-title">सम्पर्क</h3><div class="footer-links"><a href="tel:+9779844639228">+977 9844639228</a><a href="tel:+9779818234776">+977 9818234776</a><a href="mailto:shreeharijyotishparamarsakendr@gmail.com">shreeharijyotishparamarsakendr@gmail.com</a><a href="https://wa.me/9779844639228" target="_blank" rel="noopener">WhatsApp मा सन्देश</a><span>प्रधान कार्यालय: कमल-३, केर्खा, झापा</span><span>अमेरिका: 2308 Patton rd ste G, Harrisburg pa 17112 USA</span></div></div>
            </div>
            <div class="footer-bottom"><span>© <span data-year></span> श्रीहरि ज्योतिष परामर्श केन्द्र। सर्वाधिकार सुरक्षित।</span><span>www.astroshreehari.com</span></div>
        </div>
    </footer>
    <div class="float-actions">
        <a class="float-btn float-whatsapp" href="https://wa.me/9779844639228" target="_blank" rel="noopener" aria-label="WhatsApp">☏</a>
        <button class="float-btn back-top" aria-label="माथि जानुहोस्">↑</button>
    </div>
    <script src="assets/script.js"></script>
    <script>
        const kundaliForm = document.getElementById('kundaliForm');
        const kundaliResult = document.getElementById('kundaliResult');
        const kundaliCards = document.getElementById('kundaliCards');
        const kundaliStatus = document.getElementById('kundaliStatus');

        if (kundaliForm) {
            kundaliForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = kundaliForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'गणना गर्दै...';
                kundaliStatus.style.display = 'none';

                const data = {
                    name: document.getElementById('kName').value,
                    phone: document.getElementById('kPhone').value,
                    birth_date: document.getElementById('kDate').value,
                    birth_time: document.getElementById('kTime').value,
                    birth_place: document.getElementById('kPlace').value,
                };

                try {
                    const res = await fetch('https://api.astroshreehari.com/api/kundali.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await res.json();
                    if (result.success) {
                        const k = result.data.kundali;
                        kundaliCards.innerHTML = `
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">✦</div><h3>राशि</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.rashi}</p></div>
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">⌁</div><h3>नक्षत्र</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.nakshatra}</p></div>
                            <div class="card reveal" style="text-align:center"><div class="service-icon" style="margin:0 auto 16px">◉</div><h3>लग्न</h3><p style="font-size:2rem;font-weight:800;color:var(--maroon)">${k.lagna}</p></div>
                        `;
                        kundaliResult.style.display = 'block';
                        kundaliResult.scrollIntoView({ behavior: 'smooth' });
                        setTimeout(() => {
                            document.querySelectorAll('#kundaliResult .reveal').forEach(el => el.classList.add('visible'));
                        }, 100);
                    } else {
                        throw new Error(result.message);
                    }
                } catch (err) {
                    kundaliStatus.textContent = '❌ गणना गर्न समस्या भयो। कृपया पुन: प्रयास गर्नुहोस्।';
                    kundaliStatus.style.display = 'block';
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'कुण्डली हेर्नुहोस्';
                }
            });
        }
    </script>
</body>
</html>
