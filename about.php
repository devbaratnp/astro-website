<?php
require_once __DIR__ . '/includes/public-header.php';
renderPublicHeader('पण्डित तथा ज्योतिषी सीताराम तिमल्सेना | Astro Shree Hari', 'नेपाली ज्योतिषशास्त्र, आध्यात्मिक चिन्तन र पुराण वाचनको क्षेत्रमा परिचित एवं प्रतिष्ठित नाम— Astro Guru Sitaram।', '/about', ['/assets/css/pages/about.css']);
?>
<div class="about-page">
  <section class="section about-intro">
    <div class="container about-grid">
      <div class="about-photo">
        <img src="/assets/sitaram-timilsina.jpeg" alt="पण्डित तथा ज्योतिषी सीताराम तिमल्सेना" />
        <div class="experience"><strong>१०+</strong><span>वर्षको ज्योतिषीय<br />परामर्श र सेवा</span></div>
      </div>
      <div class="about-copy">
        <span class="section-kicker">परिचय</span>
        <h1>पण्डित तथा ज्योतिषी<br />सीताराम तिमल्सेना</h1>
        <p class="about-lead">नेपाली ज्योतिषशास्त्र, आध्यात्मिक चिन्तन र पुराण वाचनको क्षेत्रमा परिचित एवं प्रतिष्ठित नाम— <strong>Astro Guru Sitaram</strong>।</p>
        <p>विगत एक दशकभन्दा लामो समयदेखि ज्योतिषीय परामर्श, कर्मकाण्ड, वास्तुशास्त्र र महापुराण वाचनमार्फत उहाँले सनातन धर्म र संस्कृतिको संरक्षण एवं संवर्द्धनमा योगदान पुर्‍याउँदै आउनुभएको छ। उहाँ श्रीहरि ज्योतिष सेवा (Shreehari Jyotish Sewa) का संस्थापक तथा मुख्य परामर्शदाता हुनुहुन्छ।</p>
        <div class="about-actions">
          <a class="button button-maroon" href="/appointment"><?php echo renderIcon('ArrowRight'); ?> परामर्श बुक गर्नुहोस्</a>
          <a class="button button-outline" href="tel:+<?php echo PHONE; ?>"><?php echo renderIcon('Phone'); ?> सम्पर्क गर्नुहोस्</a>
        </div>
      </div>
    </div>
  </section>

  <section class="section biography-section">
    <div class="container biography-layout">
      <article class="biography-story">
        <span class="section-kicker">जीवन र दीक्षा</span>
        <h2>आध्यात्मिक संस्कारदेखि शास्त्रीय साधनासम्म</h2>
        <h3>जन्म तथा प्रारम्भिक जीवन</h3>
        <p>उहाँको जन्म वि.सं. २०५० साल जेठ २० गते दिउँसो १२:०० बजे ताप्लेजुङ जिल्लाको साब्लाखु गाउँमा भएको हो। पिता कृष्ण भक्त तिमल्सेना र माता जयलक्ष्मी तिमल्सेनाको धार्मिक एवं सुसंस्कृत संस्कारमा हुर्कनुभएका उहाँमा बाल्यकालदेखि नै धर्म र शास्त्रप्रति गहिरो रुचि थियो।</p>
        <h3>शैक्षिक पृष्ठभूमि</h3>
        <p>उहाँको शैक्षिक यात्रा आधुनिक शिक्षासँगै प्राचीन गुरुकुलीय पद्धतिको सुन्दर समन्वय हो।</p>
        <div class="education-list">
          <div class="education-item"><span>१</span><div><strong>प्रारम्भिक शिक्षा</strong><p>श्री सिंहदेवी प्राथमिक विद्यालय र श्री भन्ज्याङ उच्च माध्यमिक विद्यालयबाट सुरुवाती अध्ययन।</p></div></div>
          <div class="education-item"><span>२</span><div><strong>गुरुकुल शिक्षा</strong><p>अर्जुनधारा–९, झापास्थित गुरुकुल श्री कालिकापीठ कालीस्थानबाट संस्कृत, कर्मकाण्ड र धर्मशास्त्रको आधारभूत दीक्षा।</p></div></div>
          <div class="education-item"><span>३</span><div><strong>उच्च शिक्षा</strong><p>श्री सम्पूर्णानन्द संस्कृत विश्वविद्यालय, वाराणसी (काशी, भारत) बाट ज्योतिषशास्त्र, कर्मकाण्ड, पुराण वाचन र वास्तु परामर्शमा उच्च अध्ययन।</p></div></div>
        </div>
      </article>
      <aside class="profile-facts">
        <?php echo renderIcon('GraduationCap'); ?>
        <h3>विशेषज्ञताका क्षेत्र</h3>
        <ul>
          <li><?php echo renderIcon('CheckCircle'); ?> वैदिक ज्योतिष तथा जन्मकुण्डली</li>
          <li><?php echo renderIcon('CheckCircle'); ?> वास्तुशास्त्र परामर्श</li>
          <li><?php echo renderIcon('CheckCircle'); ?> कर्मकाण्ड तथा धार्मिक अनुष्ठान</li>
          <li><?php echo renderIcon('CheckCircle'); ?> पुराण वाचन तथा आध्यात्मिक प्रवचन</li>
          <li><?php echo renderIcon('CheckCircle'); ?> अनलाइन तथा प्रत्यक्ष परामर्श</li>
        </ul>
      </aside>
    </div>
  </section>

  <section class="section contribution-section">
    <div class="container">
      <div class="section-heading">
        <span>कार्यक्षेत्र र योगदान</span>
        <h2>शास्त्रीय ज्ञान, जनसेवा र आध्यात्मिक जागरण</h2>
        <p>देश–विदेशका सेवाग्राहीलाई ज्योतिषीय मार्गदर्शनसँगै सनातन परम्परा र आध्यात्मिक चेतनासँग जोड्ने निरन्तर प्रयास।</p>
      </div>
      <div class="contribution-grid">
        <article class="contribution-card"><?php echo renderIcon('Broadcast'); ?><h3>रेडियो तथा टेलिभिजन परामर्श</h3><p>विभिन्न प्रतिष्ठित सञ्चार माध्यमबाट प्रत्यक्ष ज्योतिषीय विश्लेषण तथा जनचेतनामूलक कार्यक्रम सञ्चालन।</p></article>
        <article class="contribution-card"><?php echo renderIcon('BookOpenText'); ?><h3>१३९ स्थानमा महापुराण वाचन</h3><p>नेपाल र भारतका पवित्र स्थानहरूमा श्रीमद्भागवत र ब्रह्मवैवर्तसहित १८ महापुराणको सङ्गीतमय एवं मर्मस्पर्शी वाचन।</p></article>
        <article class="contribution-card"><?php echo renderIcon('Path'); ?><h3>तीर्थयात्रा व्यवस्थापन</h3><p>नेपाल तथा भारतका प्रसिद्ध तीर्थस्थलहरूको सहज र व्यवस्थित यात्रामा भक्तजनलाई सक्रिय नेतृत्व र सहजीकरण।</p></article>
      </div>
    </div>
  </section>

  <section class="credentials about-credentials">
    <div class="container credentials-grid">
      <div><?php echo renderIcon('UsersThree'); ?><h3>दक्षिण एसियाली ज्योतिष महासङ्घ</h3><p>South Asian Astro Federation — प्रमाणित सदस्य</p></div>
      <div><?php echo renderIcon('CheckCircle'); ?><h3>विश्व ज्योतिष महासङ्घ</h3><p>Vishwa Jyotish Mahasangh — प्रमाणित सदस्य</p></div>
      <div><?php echo renderIcon('CheckCircle'); ?><h3>नेपाल ज्योतिष परिषद्</h3><p>Nepal Jyotish Parishad — प्रमाणित सदस्य</p></div>
    </div>
  </section>

  <section class="section awards-contact-section">
    <div class="container awards-contact-grid">
      <article class="award-panel">
        <?php echo renderIcon('Trophy'); ?><span>पुरस्कार तथा सम्मान</span>
        <h2>अन्तर्राष्ट्रिय सम्मानबाट विभूषित</h2>
        <p>ज्योतिष र सनातन धर्ममा पुर्‍याएको विशिष्ट योगदानको कदरस्वरूप उहाँ <strong>गोरखनाथ अवार्ड</strong>, <strong>बराहा अवार्ड</strong> लगायतका प्रतिष्ठित सम्मानबाट विभूषित हुनुहुन्छ।</p>
      </article>
      <article class="official-contact">
        <span class="section-kicker">Official Connects</span><h2>सम्पर्क र कार्यालय</h2>
        <div><?php echo renderIcon('MapPin'); ?><p><strong>प्रधान कार्यालय</strong>कमल–३, केर्खा, झापा, नेपाल</p></div>
        <div><?php echo renderIcon('MapPin'); ?><p><strong>अन्तर्राष्ट्रिय अनलाइन कार्यालय</strong>2308 Patton Road, Suite G, Harrisburg, Pennsylvania, USA</p></div>
        <div><?php echo renderIcon('Phone'); ?><p><strong>फोन / WhatsApp / Viber</strong><a href="tel:+9779844639228">+977 9844639228</a>, <a href="tel:+9779818234776">+977 9818234776</a></p></div>
        <div><?php echo renderIcon('EnvelopeSimple'); ?><p><strong>इमेल</strong><a href="mailto:<?php echo EMAIL; ?>"><?php echo EMAIL; ?></a></p></div>
      </article>
    </div>
  </section>

  <section class="english-bio">
    <div class="container english-bio-inner">
      <span>Biography in English</span><h2>Pandit &amp; Astrologer Sitaram Timalsena</h2>
      <p class="english-tagline">"Your Queries, Our Guidance: A Beacon of Vedic Astrology and Spiritual Enlightenment"</p>
      <p>Pandit &amp; Astrologer Sitaram Timalsena (Astro Guru Sitaram) is a certified Vedic astrologer, Vaastu consultant and Puran Vachak with over a decade of experience. As founder and chief consultant of Shreehari Jyotish Sewa, he guides clients around the world through astrological consultation, ritual practice, spiritual discourse and pilgrimage support.</p>
      <p>His education brings together modern schooling, traditional Gurukul training at Shree Kalikapith Kalisthan in Arjundhara, Jhapa, and advanced study in Jyotish, Karma Kanda, Vaastu Shastra and Puranic literature at Shree Sampurnanand Sanskrit University in Varanasi, India. He has delivered discourses on the 18 Mahapuranas across 139 locations in Nepal and India and has been honored with the Gorakhnath Award, Baraha Award and other distinctions.</p>
    </div>
  </section>
</div>
<?php renderPublicFooter(); ?>
