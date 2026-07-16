import { Link } from 'react-router-dom';
import {
  ArrowRight, BookOpenText, Broadcast, CheckCircle, EnvelopeSimple,
  GraduationCap, MapPin, Path, Phone, Trophy, UsersThree,
} from '@phosphor-icons/react';
import { EMAIL, PHONE } from '../constants';

const education = [
  { title: 'प्रारम्भिक शिक्षा', text: 'श्री सिंहदेवी प्राथमिक विद्यालय र श्री भन्ज्याङ उच्च माध्यमिक विद्यालयबाट सुरुवाती अध्ययन।' },
  { title: 'गुरुकुल शिक्षा', text: 'अर्जुनधारा–९, झापास्थित गुरुकुल श्री कालिकापीठ कालीस्थानबाट संस्कृत, कर्मकाण्ड र धर्मशास्त्रको आधारभूत दीक्षा।' },
  { title: 'उच्च शिक्षा', text: 'श्री सम्पूर्णानन्द संस्कृत विश्वविद्यालय, वाराणसी (काशी, भारत) बाट ज्योतिषशास्त्र, कर्मकाण्ड, पुराण वाचन र वास्तु परामर्शमा उच्च अध्ययन।' },
];

const contributions = [
  { icon: Broadcast, title: 'रेडियो तथा टेलिभिजन परामर्श', text: 'विभिन्न प्रतिष्ठित सञ्चार माध्यमबाट प्रत्यक्ष ज्योतिषीय विश्लेषण तथा जनचेतनामूलक कार्यक्रम सञ्चालन।' },
  { icon: BookOpenText, title: '१३९ स्थानमा महापुराण वाचन', text: 'नेपाल र भारतका पवित्र स्थानहरूमा श्रीमद्भागवत र ब्रह्मवैवर्तसहित १८ महापुराणको सङ्गीतमय एवं मर्मस्पर्शी वाचन।' },
  { icon: Path, title: 'तीर्थयात्रा व्यवस्थापन', text: 'नेपाल तथा भारतका प्रसिद्ध तीर्थस्थलहरूको सहज र व्यवस्थित यात्रामा भक्तजनलाई सक्रिय नेतृत्व र सहजीकरण।' },
];

export function About() {
  return (
    <main className="about-page">
      <section className="section about-intro">
        <div className="container about-grid">
          <div className="about-photo">
            <img src="/assets/sitaram-timilsina.jpeg" alt="पण्डित तथा ज्योतिषी सीताराम तिमल्सेना" />
            <div className="experience"><strong>१०+</strong><span>वर्षको ज्योतिषीय<br />परामर्श र सेवा</span></div>
          </div>
          <div className="about-copy">
            <span className="section-kicker">परिचय</span>
            <h1>पण्डित तथा ज्योतिषी<br />सीताराम तिमल्सेना</h1>
            <p className="about-lead">नेपाली ज्योतिषशास्त्र, आध्यात्मिक चिन्तन र पुराण वाचनको क्षेत्रमा परिचित एवं प्रतिष्ठित नाम— <strong>Astro Guru Sitaram</strong>।</p>
            <p>विगत एक दशकभन्दा लामो समयदेखि ज्योतिषीय परामर्श, कर्मकाण्ड, वास्तुशास्त्र र महापुराण वाचनमार्फत उहाँले सनातन धर्म र संस्कृतिको संरक्षण एवं संवर्द्धनमा योगदान पुर्‍याउँदै आउनुभएको छ। उहाँ श्रीहरि ज्योतिष सेवा (Shreehari Jyotish Sewa) का संस्थापक तथा मुख्य परामर्शदाता हुनुहुन्छ।</p>
            <div className="about-actions">
              <Link className="button button-maroon" to="/appointment"><ArrowRight /> परामर्श बुक गर्नुहोस्</Link>
              <a className="button button-outline" href={`tel:+${PHONE}`}><Phone weight="fill" /> सम्पर्क गर्नुहोस्</a>
            </div>
          </div>
        </div>
      </section>

      <section className="section biography-section">
        <div className="container biography-layout">
          <article className="biography-story">
            <span className="section-kicker">जीवन र दीक्षा</span>
            <h2>आध्यात्मिक संस्कारदेखि शास्त्रीय साधनासम्म</h2>
            <h3>जन्म तथा प्रारम्भिक जीवन</h3>
            <p>उहाँको जन्म वि.सं. २०५० साल जेठ २० गते दिउँसो १२:०० बजे ताप्लेजुङ जिल्लाको साब्लाखु गाउँमा भएको हो। पिता कृष्ण भक्त तिमल्सेना र माता जयलक्ष्मी तिमल्सेनाको धार्मिक एवं सुसंस्कृत संस्कारमा हुर्कनुभएका उहाँमा बाल्यकालदेखि नै धर्म र शास्त्रप्रति गहिरो रुचि थियो।</p>
            <h3>शैक्षिक पृष्ठभूमि</h3>
            <p>उहाँको शैक्षिक यात्रा आधुनिक शिक्षासँगै प्राचीन गुरुकुलीय पद्धतिको सुन्दर समन्वय हो।</p>
            <div className="education-list">
              {education.map(({ title, text }, index) => (
                <div key={title} className="education-item">
                  <span>{index + 1}</span>
                  <div><strong>{title}</strong><p>{text}</p></div>
                </div>
              ))}
            </div>
          </article>
          <aside className="profile-facts">
            <GraduationCap weight="duotone" />
            <h3>विशेषज्ञताका क्षेत्र</h3>
            <ul>
              <li><CheckCircle weight="fill" /> वैदिक ज्योतिष तथा जन्मकुण्डली</li>
              <li><CheckCircle weight="fill" /> वास्तुशास्त्र परामर्श</li>
              <li><CheckCircle weight="fill" /> कर्मकाण्ड तथा धार्मिक अनुष्ठान</li>
              <li><CheckCircle weight="fill" /> पुराण वाचन तथा आध्यात्मिक प्रवचन</li>
              <li><CheckCircle weight="fill" /> अनलाइन तथा प्रत्यक्ष परामर्श</li>
            </ul>
          </aside>
        </div>
      </section>

      <section className="section contribution-section">
        <div className="container">
          <div className="section-heading">
            <span>कार्यक्षेत्र र योगदान</span>
            <h2>शास्त्रीय ज्ञान, जनसेवा र आध्यात्मिक जागरण</h2>
            <p>देश–विदेशका सेवाग्राहीलाई ज्योतिषीय मार्गदर्शनसँगै सनातन परम्परा र आध्यात्मिक चेतनासँग जोड्ने निरन्तर प्रयास।</p>
          </div>
          <div className="contribution-grid">
            {contributions.map(({ icon: Icon, title, text }) => (
              <article key={title} className="contribution-card"><Icon weight="duotone" /><h3>{title}</h3><p>{text}</p></article>
            ))}
          </div>
        </div>
      </section>

      <section className="credentials about-credentials">
        <div className="container credentials-grid">
          <div><UsersThree weight="duotone" /><h3>दक्षिण एसियाली ज्योतिष महासङ्घ</h3><p>South Asian Astro Federation — प्रमाणित सदस्य</p></div>
          <div><CheckCircle weight="duotone" /><h3>विश्व ज्योतिष महासङ्घ</h3><p>Vishwa Jyotish Mahasangh — प्रमाणित सदस्य</p></div>
          <div><CheckCircle weight="duotone" /><h3>नेपाल ज्योतिष परिषद्</h3><p>Nepal Jyotish Parishad — प्रमाणित सदस्य</p></div>
        </div>
      </section>

      <section className="section awards-contact-section">
        <div className="container awards-contact-grid">
          <article className="award-panel">
            <Trophy weight="duotone" /><span>पुरस्कार तथा सम्मान</span>
            <h2>अन्तर्राष्ट्रिय सम्मानबाट विभूषित</h2>
            <p>ज्योतिष र सनातन धर्ममा पुर्‍याएको विशिष्ट योगदानको कदरस्वरूप उहाँ <strong>गोरखनाथ अवार्ड</strong>, <strong>बराहा अवार्ड</strong> लगायतका प्रतिष्ठित सम्मानबाट विभूषित हुनुहुन्छ।</p>
          </article>
          <article className="official-contact">
            <span className="section-kicker">Official Connects</span><h2>सम्पर्क र कार्यालय</h2>
            <div><MapPin weight="fill" /><p><strong>प्रधान कार्यालय</strong>कमल–३, केर्खा, झापा, नेपाल</p></div>
            <div><MapPin weight="fill" /><p><strong>अन्तर्राष्ट्रिय अनलाइन कार्यालय</strong>2308 Patton Road, Suite G, Harrisburg, Pennsylvania, USA</p></div>
            <div><Phone weight="fill" /><p><strong>फोन / WhatsApp / Viber</strong><a href={`tel:+${PHONE}`}>+977 9844639228</a>, <a href="tel:+9779818234776">+977 9818234776</a></p></div>
            <div><EnvelopeSimple weight="fill" /><p><strong>इमेल</strong><a href={`mailto:${EMAIL}`}>{EMAIL}</a></p></div>
          </article>
        </div>
      </section>

      <section className="english-bio">
        <div className="container english-bio-inner">
          <span>Biography in English</span><h2>Pandit &amp; Astrologer Sitaram Timalsena</h2>
          <p className="english-tagline">“Your Queries, Our Guidance: A Beacon of Vedic Astrology and Spiritual Enlightenment”</p>
          <p>Pandit &amp; Astrologer Sitaram Timalsena (Astro Guru Sitaram) is a certified Vedic astrologer, Vaastu consultant and Puran Vachak with over a decade of experience. As founder and chief consultant of Shreehari Jyotish Sewa, he guides clients around the world through astrological consultation, ritual practice, spiritual discourse and pilgrimage support.</p>
          <p>His education brings together modern schooling, traditional Gurukul training at Shree Kalikapith Kalisthan in Arjundhara, Jhapa, and advanced study in Jyotish, Karma Kanda, Vaastu Shastra and Puranic literature at Shree Sampurnanand Sanskrit University in Varanasi, India. He has delivered discourses on the 18 Mahapuranas across 139 locations in Nepal and India and has been honored with the Gorakhnath Award, Baraha Award and other distinctions.</p>
        </div>
      </section>
    </main>
  );
}
