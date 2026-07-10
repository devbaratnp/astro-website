<!doctype html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>दैनिक पञ्चाङ्ग | श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल</title>
    <meta name="description" content="आजको पञ्चाङ्ग — तिथि, नक्षत्र, सूर्योदय, सूर्यास्त र शुभ मुहूर्त।">
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
            <div class="nav-links"><a href="index.php">गृहपृष्ठ</a><a href="about.php">हाम्रो बारेमा</a><a href="services.php">सेवाहरू</a><a href="kundali.php">कुण्डली</a><a href="panchang.php" class="active">पञ्चाङ्ग</a><a href="appointment.php">परामर्श</a><a href="contact.php">सम्पर्क</a></div>
            <div class="nav-actions">
                <a class="btn btn-primary" href="appointment.php">परामर्श बुक गर्नुहोस्</a>
                <button class="menu-toggle" aria-label="मेनु खोल्नुहोस्" aria-expanded="false">☰</button>
            </div>
        </nav>
    </header>
    <main>
        <section class="page-hero">
            <div class="container">
                <div class="eyebrow">दैनिक पञ्चाङ्ग</div>
                <h1>आजको पञ्चाङ्ग</h1>
                <div class="breadcrumb"><a href="index.php">गृहपृष्ठ</a><span>›</span><span>पञ्चाङ्ग</span></div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="date-nav" style="text-align:center;margin-bottom:32px">
                    <button class="btn btn-outline" id="prevDay">← अघिल्लो दिन</button>
                    <span style="display:inline-block;margin:0 16px;font-weight:700;font-size:1.2rem;color:var(--maroon)" id="displayDate"></span>
                    <button class="btn btn-outline" id="nextDay">अर्को दिन →</button>
                </div>
                <div class="grid grid-4" id="panchangCards" style="text-align:center">
                    <div class="card"><div class="service-icon" style="margin:0 auto 12px">◉</div><h3>तिथि</h3><p style="font-size:1.2rem;font-weight:800;color:var(--maroon)" id="p-tithi">—</p></div>
                    <div class="card"><div class="service-icon" style="margin:0 auto 12px">⌁</div><h3>नक्षत्र</h3><p style="font-size:1.2rem;font-weight:800;color:var(--maroon)" id="p-nakshatra">—</p></div>
                    <div class="card"><div class="service-icon" style="margin:0 auto 12px">☀</div><h3>सूर्योदय</h3><p style="font-size:1.2rem;font-weight:800;color:var(--maroon)" id="p-sunrise">—</p></div>
                    <div class="card"><div class="service-icon" style="margin:0 auto 12px">☾</div><h3>सूर्यास्त</h3><p style="font-size:1.2rem;font-weight:800;color:var(--maroon)" id="p-sunset">—</p></div>
                </div>
                <div id="specialEvents" style="margin-top:24px;display:none">
                    <div class="notice" id="eventsContent"></div>
                </div>
            </div>
        </section>
        <section class="section-sm"><div class="container"><div class="cta-box reveal"><div><h2>पञ्चाङ्ग अनुसार शुभ कार्य</h2><p>विवाह, गृहप्रवेश, व्रतबन्ध जस्ता मांगलिक कार्यका लागि शुभ मुहूर्त हेर्न परामर्श लिनुहोस्।</p></div><a class="btn btn-whatsapp" href="appointment.php">परामर्श लिनुहोस्</a></div></div></section>
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
                <div><h3 class="footer-title">द्रुत लिंक</h3><div class="footer-links"><a href="about.php">हाम्रो बारेमा</a><a href="services.php">सेवाहरू</a><a href="kundali.php">कुण्डली</a><a href="panchang.php">पञ्चाङ्ग</a><a href="appointment.php">परामर्श बुकिङ</a><a href="contact.php">सम्पर्क</a></div></div>
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
        let currentDate = new Date();
        const today = currentDate.toISOString().split('T')[0];

        function formatDate(dateStr) {
            const d = new Date(dateStr + 'T12:00:00');
            const weekdays = ['आइतबार', 'सोमबार', 'मङ्गलबार', 'बुधबार', 'बिहिबार', 'शुक्रबार', 'शनिबार'];
            return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')} (${weekdays[d.getDay()]})`;
        }

        async function loadPanchang(date) {
            try {
                const res = await fetch(`https://api.astroshreehari.com/api/panchang.php?date=${date}`);
                const result = await res.json();
                if (result.success) {
                    const p = result.data.panchang;
                    document.getElementById('displayDate').textContent = formatDate(date);
                    document.getElementById('p-tithi').textContent = p.tithi;
                    document.getElementById('p-nakshatra').textContent = p.nakshatra;
                    document.getElementById('p-sunrise').textContent = p.sunrise;
                    document.getElementById('p-sunset').textContent = p.sunset;

                    const events = p.special_events;
                    if (events && events.length > 0) {
                        const el = document.getElementById('specialEvents');
                        el.style.display = 'block';
                        document.getElementById('eventsContent').innerHTML = '<strong>विशेष:</strong> ' + events.map(e => e.ne).join(', ');
                    } else {
                        document.getElementById('specialEvents').style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Panchang load error:', err);
            }
        }

        document.getElementById('prevDay').addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() - 1);
            loadPanchang(currentDate.toISOString().split('T')[0]);
        });
        document.getElementById('nextDay').addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() + 1);
            loadPanchang(currentDate.toISOString().split('T')[0]);
        });

        loadPanchang(today);
    </script>
</body>
</html>
