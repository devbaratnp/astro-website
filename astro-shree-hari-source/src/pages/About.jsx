import { Link } from 'react-router-dom';
import { CheckCircle, ArrowRight, BookOpenText, UsersThree, GraduationCap } from '@phosphor-icons/react';
import { PHONE } from '../constants';

export function About() {
  return (
    <div className="section page-section">
      <div className="container about-grid" style={{ paddingTop: '40px' }}>
        <div className="about-photo">
          <img src="/assets/sitaram-timilsina.jpeg" alt="ज्योतिषाचार्य सीताराम तिमल्सेना" />
          <div className="experience"><strong>१८+</strong><span>वर्षको अध्ययन,<br />अध्यापन र सेवा</span></div>
        </div>
        <div className="about-copy">
          <span className="section-kicker">हाम्रो बारेमा</span>
          <h2>परम्परा, अध्ययन र अनुभवको सङ्गम</h2>
          <p>पं. ज्यो. सीताराम तिमल्सेना धर्मशास्त्र, कर्मकाण्ड तथा ज्योतिषशास्त्रका विशेषज्ञ हुन्। गुरुकुलीय पद्धतिअनुसार अध्ययन गरी शास्त्रसम्मत ज्ञानलाई व्यावहारिक जीवनमा उपयोग गर्न सहज बनाउने उहाँको विशेषता हो।</p>
          <p>उहाँले १८ वर्षभन्दा बढी समयदेखि ज्योतिष परामर्श, पूजा र कर्मकाण्ड सेवा, र शास्त्रीय अध्यापनमा निरन्तरता दिँदै आउनुभएको छ। त्यसक्रममा नेपाल तथा विदेशका धेरै सेवाग्राहीहरूलाई शास्त्रसम्मत मार्गदर्शन प्रदान गरिसक्नुभएको छ।</p>
          <ul>
            <li><GraduationCap weight="fill" /> गुरुकुलीय पद्धतिमा धर्मशास्त्र, कर्मकाण्ड र ज्योतिषशास्त्र अध्ययन</li>
            <li><BookOpenText weight="fill" /> १८ महापुराणहरूको गहन अध्ययन तथा नियमित वाचन</li>
            <li><UsersThree weight="fill" /> दक्षिण एसियाली ज्योतिष महासङ्घ (SAAF) का केन्द्रीय सदस्य</li>
            <li><CheckCircle weight="fill" /> राष्ट्रिय तथा अन्तर्राष्ट्रिय मान्यता प्राप्त ज्योतिषीय सेवा</li>
            <li><CheckCircle weight="fill" /> व्यक्तिगत तथा अनलाइन परामर्शको सुविधा</li>
          </ul>
          <Link className="button button-maroon" to="/appointment"><ArrowRight /> परामर्श बुक गर्नुहोस्</Link>
        </div>
      </div>
    </div>
  );
}
