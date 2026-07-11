import { useState } from 'react';
import { CalendarBlank, WhatsappLogo, EnvelopeSimple, Clock, LockKey } from '@phosphor-icons/react';
import { PHONE, EMAIL, services } from '../constants';
import { createAppointment, getAvailableSlots } from '../services/api';

export function Appointment() {
  const [sent, setSent] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');
  const [slots, setSlots] = useState([]);
  const [meetingUrl, setMeetingUrl] = useState('');

  const submit = async (event) => {
    event.preventDefault();
    setSubmitting(true);
    setError('');
    const data = new FormData(event.currentTarget);
    try {
      const response = await createAppointment({
        name: data.get('name'),
        phone: data.get('phone'),
        email: data.get('email') || '',
        service_type: data.get('service'),
        message: data.get('message'),
        preferred_date: data.get('preferred_date'), preferred_time: data.get('preferred_time'),
        consultation_mode: data.get('consultation_mode'),
      });
      setMeetingUrl(response.data.meeting_url || '');
    } catch (error) {
      setError(error.message || 'अनुरोध सुरक्षित गर्न सकिएन। कृपया फेरि प्रयास गर्नुहोस्।');
      setSubmitting(false);
      return;
    }
    const message = `नमस्कार गुरुज्यू, म ${data.get('name')} हुँ।\nफोन: ${data.get('phone')}\nसेवा: ${data.get('service')}\nप्रश्न: ${data.get('message')}`;
    setSent(true);
    setSubmitting(false);
    window.open(`https://wa.me/${PHONE}?text=${encodeURIComponent(message)}`, '_blank', 'noopener,noreferrer');
  };

  return (
    <section className="section page-section">
      <div className="container contact-grid" style={{ paddingTop: '40px' }}>
        <div className="contact-card">
          <span className="section-kicker">आजै सम्पर्क गर्नुहोस्</span>
          <h2>परामर्श बुक गर्नुहोस्</h2>
          <p>आफ्नो समय बुक गरी शास्त्रसम्मत मार्गदर्शन लिनुहोस्।</p>
          <a href={`https://wa.me/${PHONE}`} target="_blank" rel="noreferrer">
            <WhatsappLogo weight="fill" /><span>WhatsApp<strong>+977 9844639228</strong></span>
          </a>
          <a href={`mailto:${EMAIL}`}>
            <EnvelopeSimple /><span>इमेल<strong>{EMAIL}</strong></span>
          </a>
          <div>
            <Clock /><span>परामर्श माध्यम<strong>प्रत्यक्ष तथा अनलाइन</strong></span>
          </div>
        </div>

        <form className="booking-form" onSubmit={submit}>
          <div className="form-title"><span>शुभ लाभ</span><h3>आफ्नो विवरण पठाउनुहोस्</h3></div>
          <div className="form-grid">
            <label>तपाईंको नाम *<input name="name" required placeholder="पूरा नाम लेख्नुहोस्" /></label>
            <label>फोन नम्बर *<input name="phone" required inputMode="tel" placeholder="98XXXXXXXX" /></label>
            <label>सेवा छान्नुहोस् *<select name="service" required defaultValue="">
              <option value="" disabled>सेवा छान्नुहोस्</option>
              {services.map((s, index) => <option key={s.title} value={['kundali','marriage','grahadasha','vastu','pooja','general'][index]}>{s.title}</option>)}
            </select></label>
            <label>इमेल<input name="email" type="email" placeholder="तपाईंको इमेल" /></label>
            <label>मिति *<input name="preferred_date" type="date" min={new Date().toISOString().slice(0,10)} required onChange={async e=>{try{const x=await getAvailableSlots(e.target.value);setSlots(x.data.available_slots)}catch(_){setSlots([])}}}/></label>
            <label>समय *<select name="preferred_time" required defaultValue=""><option value="" disabled>समय छान्नुहोस्</option>{slots.map(x=><option key={x} value={x}>{x}</option>)}</select></label>
            <label>परामर्श माध्यम<select name="consultation_mode" defaultValue="whatsapp"><option value="whatsapp">WhatsApp</option><option value="video">Video consultation</option><option value="phone">Phone</option><option value="inperson">प्रत्यक्ष</option></select></label>
            <label className="full">तपाईंको प्रश्न / समस्या *<textarea name="message" required rows="4" placeholder="आफ्नो प्रश्न वा समस्या विस्तारमा लेख्नुहोस्..." /></label>
          </div>
          <button className="button button-maroon full-button" type="submit" disabled={submitting}>
            <CalendarBlank weight="bold" /> {submitting ? 'पठाउँदै...' : 'WhatsApp मार्फत अनुरोध पठाउनुहोस्'}
          </button>
          {sent && <p className="success">WhatsApp खुल्दैछ। कृपया सन्देश पठाउनुहोस्।</p>}
          {meetingUrl&&<p className="success">Video room: <a href={meetingUrl} target="_blank" rel="noreferrer">सुरक्षित भिडियो परामर्श खोल्नुहोस्</a></p>}
          {error && <p className="form-error">{error}</p>}
          <small><LockKey /> तपाईंको जानकारी पूर्ण गोपनीय र सुरक्षित रहनेछ।</small>
        </form>
      </div>
    </section>
  );
}
