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
