import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import {
  CalendarBlank, WhatsappLogo, ShieldCheck, Monitor,
  ChartPolar, BookOpenText, UsersThree, GraduationCap,
  CheckCircle, Quotes, ArrowRight, LockKey, CalendarDots,
  Campfire, Compass, Planet, Heart
} from '@phosphor-icons/react';
import { PHONE, services } from '../constants';
import { getPanchang, getTestimonials } from '../services/api';
import { buildHomePanchangItems, getBsDateString } from '../utils/homeAstrology';
import { ad2bs } from '../utils/bsDate';
import '../homeAstrology.css';

const homeTools = [
  { to: '/panchang', icon: CalendarDots, title: 'आजको पञ्चाङ्ग', text: 'तिथि, नक्षत्र, सूर्योदय, सूर्यास्त र दैनिक राशिफल हेर्नुहोस्।', link: 'आजको विवरण' },
  { to: '/kundali', icon: ChartPolar, title: 'जन्मकुण्डली', text: 'आफ्नो जन्म विवरणका आधारमा प्रारम्भिक ज्योतिषीय विवरण पाउनुहोस्।', link: 'कुण्डली बनाउनुहोस्' },
  { to: '/pooja', icon: Campfire, title: 'पूजा तथा मुहूर्त', text: 'वैदिक पूजा सेवा, अनुष्ठान र शुभ समयसम्बन्धी जानकारी लिनुहोस्।', link: 'सेवा हेर्नुहोस्' },
  { to: '/appointment', icon: Heart, title: 'विवाह तथा परामर्श', text: 'विवाह मिलान वा व्यक्तिगत जिज्ञासाका लागि गुरुज्यूसँग समय लिनुहोस्।', link: 'परामर्श बुक गर्नुहोस्' },
];

export function Home() {
  const [panchangItems, setPanchangItems] = useState([]);
  const [panchangStatus, setPanchangStatus] = useState('loading');
  const [panchangRefresh, setPanchangRefresh] = useState(0);
  const [testimonials, setTestimonials] = useState([]);
  const [clock, setClock] = useState('');

  useEffect(() => {
    let active = true;
    setPanchangStatus('loading');
    getPanchang()
      .then((response) => {
        if (!active) return;
        setPanchangItems(buildHomePanchangItems(response.panchang));
        setPanchangStatus('ready');
      })
      .catch(() => {
        if (!active) return;
        setPanchangStatus('error');
      });
    getTestimonials().then(d => { if (active) setTestimonials(d); }).catch(() => {});
    return () => { active = false; };
  }, [panchangRefresh]);

  useEffect(() => {
    const fmt = new Intl.DateTimeFormat('ne-NP', {
      hour: '2-digit', minute: '2-digit', second: '2-digit',
      timeZone: 'Asia/Kathmandu', hour12: false,
    });
    const tick = () => setClock(fmt.format(new Date()));
    tick();
    const id = setInterval(tick, 1000);
    return () => clearInterval(id);
  }, []);

  const today = new Date();
  const todayLabel = new Intl.DateTimeFormat('ne-NP', {
    dateStyle: 'full',
    timeZone: 'Asia/Kathmandu',
  }).format(today);
  const bsToday = getBsDateString(ad2bs(today));

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

      <section className="section astro-hub-section" aria-labelledby="astro-hub-title">
        <div className="container">
          <div className="astro-hub-heading">
            <div>
              <span className="section-kicker">दैनिक मार्गदर्शन र उपयोगी सेवा</span>
              <h2 id="astro-hub-title">आजको ज्योतिष एकै स्थानमा</h2>
              <p>दैनिक पञ्चाङ्ग हेर्नुहोस्, आवश्यक ज्योतिषीय सेवा छान्नुहोस् र विस्तृत परामर्शका लागि सहज रूपमा अगाडि बढ्नुहोस्।</p>
            </div>
            <Link className="button button-maroon" to="/appointment"><CalendarBlank weight="bold" /> परामर्श बुक गर्नुहोस्</Link>
          </div>

          <div className="astro-hub-layout">
            <article className="astro-daily-card">
                <div className="astro-daily-topline">
                  <div className="astro-daily-symbol"><Planet weight="duotone" /></div>
                  <div>
                    <span>आजको पञ्चाङ्ग</span>
                    <strong className="astro-bs-date">{bsToday}</strong>
                    <span className="astro-en-date">{todayLabel}</span>
                    <span className="astro-clock">{clock}</span>
                  </div>
                </div>

              <div className="astro-panchang-status" aria-live="polite">
                {panchangStatus === 'loading' && <div className="astro-loading"><i></i><span>आजको पञ्चाङ्ग लोड हुँदैछ…</span></div>}
                {panchangStatus === 'error' && (
                  <div className="astro-error">
                    <p>अहिले पञ्चाङ्ग प्राप्त गर्न सकिएन।</p>
                    <button type="button" onClick={() => setPanchangRefresh((value) => value + 1)}>फेरि प्रयास गर्नुहोस्</button>
                  </div>
                )}
                {panchangStatus === 'ready' && (
                  <div className="astro-panchang-grid">
                    {panchangItems.map((item) => (
                      <div className={`astro-panchang-item astro-${item.key}`} key={item.key}>
                        <span>{item.label}</span>
                        <strong>{item.value}</strong>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              <Link className="astro-daily-link" to="/panchang">पूर्ण पञ्चाङ्ग र १२ राशिको राशिफल <ArrowRight weight="bold" /></Link>
              <small>पञ्चाङ्ग विवरण सामान्य जानकारीका लागि हो। व्यक्तिगत निर्णयका लागि विशेषज्ञ परामर्श लिनुहोस्।</small>
            </article>

            <div className="astro-tool-grid">
              {homeTools.map(({ to, icon: Icon, title, text, link }) => (
                <Link className="astro-tool-card" to={to} key={title}>
                  <span className="astro-tool-icon"><Icon weight="duotone" /></span>
                  <div>
                    <h3>{title}</h3>
                    <p>{text}</p>
                    <strong>{link} <ArrowRight weight="bold" /></strong>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </div>
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

      {testimonials.length > 0 && <section className="section testimonial-section">
        <div className="container">
          <div className="section-heading"><span>सेवाग्राहीको प्रतिक्रिया</span><h2>हाम्रा सन्तुष्ट सेवाग्राही</h2></div>
          <div className="testimonial-grid">
            {testimonials.map(t => (
              <div className="testimonial" key={t.id}>
                <Quotes weight="fill" />
                <p>“{t.content}”</p>
                <strong>{t.name}</strong>
                {t.location && <span>{t.location}</span>}
              </div>
            ))}
          </div>
        </div>
      </section>}
    </>
  );
}
