import { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import {
  Phone, WhatsappLogo, YoutubeLogo, FacebookLogo, List, X, MapPin, EnvelopeSimple
} from '@phosphor-icons/react';
import { PHONE, EMAIL } from '../constants';

function Logo() {
  return (
    <Link to="/" className="brand" aria-label="Astro Shree Hari home">
      <span className="brand-mark">ॐ</span>
      <span>
        <strong>Astro Shree Hari</strong>
        <small>श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल</small>
      </span>
    </Link>
  );
}

const links = [
  { to: '/', label: 'गृहपृष्ठ' },
  { to: '/about', label: 'हाम्रो बारेमा' },
  { to: '/services', label: 'सेवाहरू' },
  { to: '/kundali', label: 'कुण्डली' },
  { to: '/pooja', label: 'ई-पूजा' },
  { to: '/panchang', label: 'पञ्चाङ्ग' },
  { to: '/appointment', label: 'परामर्श प्रक्रिया' },
  { to: '/contact', label: 'सम्पर्क' },
];

export function Layout({ children }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const { pathname } = useLocation();
  const closeMenu = () => setMenuOpen(false);
  useEffect(() => {
    const meta = {'/':['Best Astrologer in Nepal | Astro Shree Hari','Traditional astrology, Kundali and online consultation in Nepal.'],'/appointment':['Online Astrology Consultation Nepal','Book an appointment and secure video consultation.'],'/kundali':['Online Kundali Generation','Enter birth details for basic Rashi, Nakshatra and Lagna.'],'/pooja':['Online Pooja Booking Nepal','Book priests, ritual materials and live-streamed Pooja.'],'/panchang':['Daily Nepali Panchang and Rashifal','Today’s Tithi, Nakshatra, sunrise, sunset and horoscope.'],'/payment':['Secure Static QR Payment','Submit a static QR payment reference for verification.']}[pathname] || ['Astro Shree Hari','Astrology and Vedic ritual services in Nepal.'];
    document.title=meta[0]; document.querySelector('meta[name="description"]')?.setAttribute('content',meta[1]); document.querySelector('link[rel="canonical"]')?.setAttribute('href',`https://www.astroshreehari.com${pathname}`);
  },[pathname]);

  return (
    <div className="site-shell">
      <header className="header">
        <div className="nav-wrap">
          <Logo />
          <button className="menu-button" onClick={() => setMenuOpen(!menuOpen)} aria-label="Menu" aria-expanded={menuOpen}>
            {menuOpen ? <X /> : <List />}
          </button>
          <nav className={menuOpen ? 'nav open' : 'nav'}>
            {links.map(l => (
              <Link key={l.to} to={l.to} onClick={closeMenu} className={pathname === l.to ? 'active' : ''}>
                {l.label}
              </Link>
            ))}
          </nav>
          <a className="phone-link" href={`tel:+${PHONE}`}><Phone weight="fill" /> +977 9844639228</a>
          <Link className="button button-gold nav-cta" to="/appointment">परामर्श बुक गर्नुहोस्</Link>
        </div>
      </header>

      <main>{children}</main>

      <footer className="footer">
        <div className="container footer-grid">
          <div>
            <Logo />
            <p>शास्त्र, संस्कार र जीवनका लागि विश्वसनीय मार्गदर्शन। धर्मशास्त्र, कर्मकाण्ड र ज्योतिषशास्त्रमा आधारित सेवा।</p>
            <div className="socials">
              <a href="https://youtube.com/@astrogurusitaram3m" target="_blank" rel="noreferrer" aria-label="YouTube"><YoutubeLogo /></a>
              <a href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noreferrer" aria-label="Facebook"><FacebookLogo /></a>
              <a href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer" aria-label="WhatsApp"><WhatsappLogo /></a>
            </div>
          </div>
          <div>
            <h3>द्रुत लिङ्कहरू</h3>
            <Link to="/">गृहपृष्ठ</Link>
            <Link to="/about">हाम्रो बारेमा</Link>
            <Link to="/services">सेवाहरू</Link>
            <Link to="/appointment">परामर्श प्रक्रिया</Link>
            <Link to="/payment">भुक्तानी</Link>
          </div>
          <div>
            <h3>सेवाहरू</h3>
            <Link to="/services">जन्मकुण्डली विश्लेषण</Link>
            <Link to="/services">विवाह मिलान</Link>
            <Link to="/services">ग्रह शान्ति</Link>
            <Link to="/services">वैदिक कर्मकाण्ड</Link>
          </div>
          <div>
            <h3>सम्पर्क जानकारी</h3>
            <a href={`tel:+${PHONE}`}><Phone /> +977 9844639228</a>
            <a href={`mailto:${EMAIL}`}><EnvelopeSimple /> {EMAIL}</a>
            <span><MapPin /> नेपाल</span>
          </div>
        </div>
        <div className="copyright container">© २०२६ Astro Shree Hari. सर्वाधिकार सुरक्षित।</div>
      </footer>

      <a className="floating-whatsapp" href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer" aria-label="WhatsApp">
        <WhatsappLogo weight="fill" />
      </a>
    </div>
  );
}
