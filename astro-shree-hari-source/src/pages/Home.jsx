import { Link } from 'react-router-dom';
import {
  CalendarBlank, WhatsappLogo, ShieldCheck, Monitor,
  ChartPolar, BookOpenText, UsersThree, GraduationCap,
  CheckCircle, Quotes, ArrowRight, LockKey, CalendarDots,
  Campfire, Compass, Planet, Heart
} from '@phosphor-icons/react';
import { PHONE, services } from '../constants';

export function Home() {
  return (
    <>
      <section id="home" className="hero">
        <div className="container hero-grid">
          <div className="hero-copy">
            <span className="eyebrow">शास्त्रसम्मत ज्योतिषीय परामर्श</span>
            <h1>तपाईँको जिज्ञासा हाम्रो परामर्श<br /><em>श्रीहरि ज्योतिष परामर्श केन्द्र नेपाल</em></h1>
            <p className="hero-name-en">Nepali Astrologer Sitaram Timalsena</p>
            <p>धर्मशास्त्र, कर्मकाण्ड तथा ज्योतिषशास्त्रसँग सम्बन्धित गुरुकुलीय पद्धति अनुसारको अध्ययन र अध्यापनको लामो अनुभव।</p>
            <div className="hero-actions">
              <Link className="button button-maroon" to="/appointment"><CalendarBlank weight="bold" /> परामर्श बुक गर्नुहोस्</Link>
              <a className="button button-outline" href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer"><WhatsappLogo weight="fill" /> WhatsApp मा सम्पर्क गर्नुहोस्</a>
            </div>
            <div className="rating"><span className="avatars"><i>श्री</i><i>ॐ</i><i>शुभ</i></span><span>विश्वसनीय धार्मिक तथा ज्योतिषीय सेवा</span></div>
          </div>
          <div className="portrait-wrap">
            <div className="portrait-ring"></div>
            <img src="/assets/sitaram-timilsina.jpeg" alt="पं. ज्यो. सीताराम तिमल्सेना" />
            <div className="name-plaque"><strong>पं. ज्यो. सीताराम तिमल्सेना</strong><span>नेपाली ज्योतिष तथा कर्मकाण्ड विशेषज्ञ</span></div>
          </div>
        </div>
      </section>

      <section className="trust-bar container">
        <div><GraduationCap /><span><strong>गुरुकुलीय पद्धति</strong>अध्ययन तथा अध्यापन</span></div>
        <div><BookOpenText /><span><strong>१८ महापुराण</strong>अध्ययन तथा वाचन</span></div>
        <div><UsersThree /><span><strong>केन्द्रीय सदस्य</strong>दक्षिण एसियाली ज्योतिष महासङ्घ</span></div>
      </section>

      <section id="services" className="section services-section">
        <div className="container">
          <div className="section-heading">
            <span>हाम्रा प्रमुख सेवाहरू</span>
            <h2>जीवनका हरेक पक्षका लागि वैदिक समाधान</h2>
            <p>शास्त्रसम्मत विधि, अनुभव र गोपनीयतामा आधारित व्यक्तिगत सेवा</p>
          </div>
          <div className="service-grid">
            {services.map(({ icon: Icon, title, text }) => (
              <article className="service-card" key={title}>
                <Icon weight="thin" />
                <h3>{title}</h3>
                <p>{text}</p>
                <Link to="/appointment">परामर्श लिनुहोस् <ArrowRight /></Link>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section id="about" className="section about-section">
        <div className="container about-grid">
          <div className="about-photo org-logo-panel">
            <img src="/assets/shreehari-logo.webp" alt="श्रीहरि ज्योतिष परामर्श केन्द्र नेपालको लोगो" />
            <div className="organization-badge"><strong>श्रीहरि</strong><span>ज्योतिष परामर्श केन्द्र नेपाल</span></div>
          </div>
          <div className="about-copy">
            <span className="section-kicker">हाम्रो बारेमा</span>
            <h2>शास्त्रीय परम्परा र विश्वसनीय परामर्शको केन्द्र</h2>
            <p>श्रीहरि ज्योतिष परामर्श केन्द्र नेपाल वैदिक ज्योतिष, वास्तुशास्त्र, कर्मकाण्ड र आध्यात्मिक सेवाका लागि समर्पित संस्था हो। पण्डित तथा ज्योतिषी सीताराम तिमल्सेनाको नेतृत्वमा केन्द्रले शास्त्रसम्मत ज्ञानलाई सरल, व्यावहारिक र सेवाग्राहीको जीवनसँग उपयोगी हुने गरी प्रस्तुत गर्दछ।</p>
            <p>नेपालस्थित प्रधान कार्यालय र अमेरिकास्थित अन्तर्राष्ट्रिय अनलाइन कार्यालयमार्फत देश–विदेशका सेवाग्राहीलाई प्रत्यक्ष तथा अनलाइन परामर्श उपलब्ध छ।</p>
            <ul>
              <li><CheckCircle weight="fill" /> जन्मकुण्डली, विवाह मिलान तथा ग्रहगोचर विश्लेषण</li>
              <li><CheckCircle weight="fill" /> वास्तु परामर्श, पूजा तथा वैदिक कर्मकाण्ड</li>
              <li><CheckCircle weight="fill" /> महापुराण वाचन र तीर्थयात्रा सहजीकरण</li>
              <li><CheckCircle weight="fill" /> नेपाल तथा विदेशमा अनलाइन परामर्श सेवा</li>
            </ul>
            <Link className="text-link" to="/about">संस्था र गुरुज्यूको विस्तृत परिचय <ArrowRight /></Link>
          </div>
        </div>
      </section>

      <section className="credentials">
        <div className="container credentials-grid">
          <div><ShieldCheck weight="thin" /><h3>शास्त्रसम्मत मार्गदर्शन</h3><p>धर्मशास्त्रीय आधार र परम्परागत वैदिक विधिमा आधारित सेवा।</p></div>
          <div><Monitor weight="thin" /><h3>अनलाइन सेवा</h3><p>देश वा विदेशबाट WhatsApp मार्फत सहज परामर्श।</p></div>
          <div><LockKey weight="thin" /><h3>पूर्ण गोपनीयता</h3><p>तपाईंको व्यक्तिगत विवरण र परामर्श पूर्ण रूपमा सुरक्षित।</p></div>
        </div>
      </section>

      <section id="process" className="section process-section">
        <div className="container">
          <div className="section-heading"><span>सरल र सहज</span><h2>परामर्श प्रक्रिया</h2></div>
          <div className="steps">
            <div><b>०१</b><CalendarBlank /><h3>समय छान्नुहोस्</h3><p>उपलब्ध समयअनुसार आफ्नो समय बुक गर्नुहोस्</p></div>
            <div><b>०२</b><BookOpenText /><h3>विवरण पठाउनुहोस्</h3><p>जन्म विवरण र आफ्नो मुख्य प्रश्न लेख्नुहोस्</p></div>
            <div><b>०३</b><ChartPolar /><h3>विश्लेषण</h3><p>गुरुज्यूले शास्त्रसम्मत अध्ययन गर्नुहुन्छ</p></div>
            <div><b>०४</b><WhatsappLogo /><h3>परामर्श सत्र</h3><p>प्रत्यक्ष वा अनलाइन परामर्श लिनुहोस्</p></div>
          </div>
        </div>
      </section>

      <section className="section testimonial-section">
        <div className="container">
          <div className="testimonial">
            <Quotes weight="fill" />
            <p>“गुरुज्यूले हाम्रो प्रश्नलाई धैर्यपूर्वक सुनेर स्पष्ट र व्यावहारिक मार्गदर्शन दिनुभयो। परामर्शपछि निर्णय लिन धेरै सहज भयो।”</p>
            <strong>सन्तुष्ट सेवाग्राही</strong>
            <span>काठमाडौं, नेपाल</span>
          </div>
        </div>
      </section>
    </>
  );
}
