import { useState } from 'react';
import { Phone, WhatsappLogo, EnvelopeSimple, MapPin, Clock, YoutubeLogo, FacebookLogo } from '@phosphor-icons/react';
import { PHONE, EMAIL } from '../constants';
import { sendContactMessage } from '../services/api';

export function Contact() {
  const [state, setState] = useState({loading:false, success:'', error:''});
  async function submit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    setState({loading:true, success:'', error:''});
    try {
      await sendContactMessage(data);
      form.reset();
      setState({loading:false, success:'सन्देश सफलतापूर्वक पठाइयो।', error:''});
    } catch (error) {
      setState({loading:false, success:'', error:error.message || 'सन्देश पठाउन सकिएन।'});
    }
  }
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

          <div className="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3563.048259485282!2d87.68542367532926!3d26.738298676745547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sne!2snp!4v1722334567890" width="100%" height="250" style={{border:0,borderRadius:'10px'}} allowFullScreen="" loading="lazy" referrerPolicy="no-referrer-when-downgrade" title="श्रीहरि ज्योतिष परामर्श केन्द्र — झापा"></iframe>
          </div>

          <form className="booking-form" onSubmit={submit}>
            <div className="form-title"><span>सन्देश</span><h3>हामीलाई लेख्नुहोस्</h3></div>
            <div className="form-grid">
              <label>नाम *<input name="name" required maxLength="100" /></label>
              <label>फोन<input name="phone" inputMode="tel" maxLength="20" /></label>
              <label className="full">इमेल<input name="email" type="email" maxLength="100" /></label>
              <label className="full">विषय *<input name="subject" required maxLength="200" /></label>
              <label className="full">सन्देश *<textarea name="message" required rows="5" /></label>
            </div>
            <button className="button button-maroon full-button" disabled={state.loading}>{state.loading?'पठाउँदै…':'सन्देश पठाउनुहोस्'}</button>
            {state.success&&<p className="success">{state.success}</p>}{state.error&&<p className="form-error">{state.error}</p>}
          </form>
        </div>
      </div>
    </section>
  );
}
