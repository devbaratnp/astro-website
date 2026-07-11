import { Phone, WhatsappLogo, EnvelopeSimple, MapPin, Clock, YoutubeLogo, FacebookLogo } from '@phosphor-icons/react';
import { PHONE, EMAIL } from '../constants';

export function Contact() {
  return (
    <section className="section page-section">
      <div className="container" style={{ paddingTop: '40px' }}>
        <div className="section-heading">
          <span>सम्पर्क</span>
          <h2>हामीसँग जोडिनुहोस्</h2>
          <p>तपाईंको प्रश्न, सुझाव वा परामर्शका लागि हामीलाई सम्पर्क गर्नुहोस्।</p>
        </div>

        <div className="contact-grid" style={{ marginTop: '30px' }}>
          <div className="contact-card">
            <h3>सम्पर्क विवरण</h3>
            <a href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer">
              <WhatsappLogo weight="fill" /><span>WhatsApp<strong>+977 9844639228</strong></span>
            </a>
            <a href={`tel:+${PHONE}`}>
              <Phone /><span>फोन<strong>+977 9844639228</strong></span>
            </a>
            <a href={`mailto:${EMAIL}`}>
              <EnvelopeSimple /><span>इमेल<strong>{EMAIL}</strong></span>
            </a>
            <div>
              <MapPin /><span>प्रधान कार्यालय<strong>कमल-३, केर्खा, झापा, नेपाल</strong></span>
            </div>
            <div>
              <Clock /><span>परामर्श माध्यम<strong>प्रत्यक्ष तथा अनलाइन (WhatsApp/Video)</strong></span>
            </div>
            <div className="socials" style={{ marginTop: '16px' }}>
              <a href="https://youtube.com/@astrogurusitaram3m" target="_blank" rel="noreferrer" aria-label="YouTube"><YoutubeLogo /></a>
              <a href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noreferrer" aria-label="Facebook"><FacebookLogo /></a>
              <a href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer" aria-label="WhatsApp"><WhatsappLogo /></a>
            </div>
          </div>

          <div className="contact-card" style={{ background: 'var(--cream)' }}>
            <h3>कार्यालय समय</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '16px' }}>
              <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                <span>आइतबार – शुक्रबार</span>
                <strong>बिहान ९:०० – साँझ ६:००</strong>
              </div>
              <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                <span>शनिबार</span>
                <strong>बिहान ९:०० – दिउँसो १२:००</strong>
              </div>
            </div>
            <p style={{ marginTop: '20px', fontSize: '14px', color: 'var(--muted)' }}>
              अनलाइन परामर्शको लागि पूर्व समय मिलाउन WhatsApp मा सम्पर्क गर्नुहोस्।
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}
