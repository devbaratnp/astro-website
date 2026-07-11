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
            <h1>जीवनका प्रश्नहरूमा<br /><em>शास्त्रीय मार्गदर्शन</em></h1>
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
          <div className="about-photo">
            <img src="/assets/sitaram-timilsina.jpeg" alt="ज्योतिषाचार्य सीताराम तिमल्सेना" />
            <div className="experience"><strong>१८+</strong><span>वर्षको अध्ययन,<br />अध्यापन र सेवा</span></div>
          </div>
          <div className="about-copy">
            <span className="section-kicker">हाम्रो बारेमा</span>
            <h2>परम्परा, अध्ययन र अनुभवको सङ्गम</h2>
            <p>पं. ज्यो. सीताराम तिमल्सेनाले धर्मशास्त्र, कर्मकाण्ड तथा ज्योतिषशास्त्रको गुरुकुलीय पद्धतिअनुसार अध्ययनसँगै अध्यापन र परामर्श सेवा प्रदान गर्दै आउनुभएको छ। शास्त्रसम्मत सिद्धान्त, व्यावहारिक अनुभव र सेवाग्राहीको गोपनीयतालाई केन्द्रमा राखेर स्पष्ट तथा उपयोगी मार्गदर्शन दिनु उहाँको प्रमुख विशेषता हो।</p>
            <ul>
              <li><CheckCircle weight="fill" /> १८ महापुराणहरूको अध्ययन तथा वाचन</li>
              <li><CheckCircle weight="fill" /> दक्षिण एसियाली ज्योतिष महासङ्घ केन्द्रीय सदस्य</li>
              <li><CheckCircle weight="fill" /> राष्ट्रिय तथा अन्तर्राष्ट्रिय मान्यता प्राप्त ज्योतिषीय सेवा</li>
              <li><CheckCircle weight="fill" /> अनलाइन तथा प्रत्यक्ष परामर्श उपलब्ध</li>
            </ul>
            <Link className="text-link" to="/about">विस्तृत परिचय हेर्नुहोस् <ArrowRight /></Link>
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
