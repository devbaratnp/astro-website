import { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import {
  Phone, WhatsappLogo, YoutubeLogo, FacebookLogo, List, X, MapPin, EnvelopeSimple
} from '@phosphor-icons/react';
import { PHONE, EMAIL } from '../constants';

function Logo() {
  return (
    <Link to="/" className="brand" aria-label="Astro Shree Hari home">
      <img className="brand-logo" src="/assets/shreehari-logo.webp" alt="श्रीहरि ज्योतिष लोगो" width="56" height="56" />
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
  { to: '/panchang', label: 'पञ्चाङ्ग' },
  { to: '/muhurta', label: 'मुहूर्त' },
  { to: '/blog', label: 'लेख' },
  { to: '/events', label: 'कार्यक्रम' },
  { to: '/gallery', label: 'ग्यालेरी' },
  { to: '/pooja', label: 'ई-पूजा' },
  { to: '/store', label: 'पूजा भण्डार' },
  { to: '/appointment', label: 'परामर्श प्रक्रिया' },
  { to: '/contact', label: 'सम्पर्क' },
];

export function Layout({ children }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const { pathname } = useLocation();
  const closeMenu = () => setMenuOpen(false);
  useEffect(() => {
    const meta = {'/':['Best Astrologer in Nepal | Astro Shree Hari','Traditional astrology, Kundali and online consultation in Nepal.'],'/appointment':['Online Astrology Consultation Nepal','Book an appointment and secure video consultation.'],'/kundali':['Online Kundali Generation','Enter birth details for basic Rashi, Nakshatra and Lagna.'],'/pooja':['Online Pooja Booking Nepal','Book priests, ritual materials and live-streamed Pooja.'],'/panchang':['Daily Nepali Panchang and Rashifal','Today’s Tithi, Nakshatra, sunrise, sunset and horoscope.'],'/payment':['Secure Static QR Payment','Submit a static QR payment reference for verification.'],'/blog':['Nepali Astrology Blog | Astro Shree Hari','Read articles on Vedic astrology, spiritual wisdom and Sanatan culture.'],'/events':['Upcoming Events & Pilgrimage Tours','Spiritual discourses, rituals and pilgrimage tour schedules.'],'/gallery':['Media Gallery | Astro Shree Hari','Video and photo gallery of discourses, bhajans and events.'],'/muhurta':['Muhurta Check | Auspicious Timing','Check auspicious timing for marriage, grihapravesh, business and travel.']}[pathname] || ['Astro Shree Hari','Astrology and Vedic ritual services in Nepal.'];
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
          <div className="footer-brand-block">
            <Link to="/" className="footer-brand" aria-label="श्रीहरि ज्योतिष गृहपृष्ठ">
              <img src="/assets/shreehari-logo.webp" alt="श्रीहरि ज्योतिष लोगो" width="64" height="64" />
              <span><strong>श्रीहरि पूजा भण्डार</strong><b>एवं ज्योतिष परामर्श केन्द्र नेपाल</b></span>
            </Link>
            <p>शास्त्र, संस्कार र जीवनका लागि विश्वसनीय मार्गदर्शन। धर्मशास्त्र, कर्मकाण्ड र ज्योतिषशास्त्रमा आधारित सेवा।</p>
          </div>
          <div className="footer-column">
            <h3>द्रुत लिङ्कहरू</h3>
            <Link to="/">गृहपृष्ठ</Link>
            <Link to="/about">हाम्रो बारेमा</Link>
            <Link to="/services">सेवाहरू</Link>
            <Link to="/blog">लेख तथा रचनाहरू</Link>
            <Link to="/events">कार्यक्रम तथा यात्रा</Link>
            <Link to="/gallery">मिडिया ग्यालेरी</Link>
            <Link to="/muhurta">मुहूर्त परीक्षण</Link>
            <Link to="/store">पूजा भण्डार</Link>
            <Link to="/appointment">परामर्श प्रक्रिया</Link>
            <Link to="/contact">सम्पर्क</Link>
          </div>
          <div className="footer-column footer-contact">
            <h3>सम्पर्क जानकारी</h3>
            <a href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer"><WhatsappLogo weight="fill" /><span>+977 9844639228</span></a>
            <a href={`mailto:${EMAIL}`}><EnvelopeSimple weight="fill" /><span>{EMAIL}</span></a>
            <span><MapPin weight="fill" /><span>कमल–३, केर्खा, झापा, नेपाल</span></span>
          </div>
          <div className="footer-column footer-social-column">
            <h3>हाम्रा सामाजिक सञ्जाल</h3>
            <div className="footer-social-links">
              <a href="https://youtube.com/@astrogurusitaram3m" target="_blank" rel="noreferrer" aria-label="YouTube"><YoutubeLogo weight="fill" /><span>YouTube</span></a>
              <a href="https://www.facebook.com/share/19AnGtrMox/" target="_blank" rel="noreferrer" aria-label="Facebook"><FacebookLogo weight="fill" /><span>Facebook</span></a>
            </div>
          </div>
        </div>
        <div className="copyright">© २०२६ श्रीहरि पूजा भण्डार एवं ज्योतिष परामर्श केन्द्र नेपाल । सर्वाधिकार सुरक्षित।</div>
      </footer>

      <a className="floating-whatsapp" href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer" aria-label="WhatsApp">
        <WhatsappLogo weight="fill" />
      </a>
    </div>
  );
}
