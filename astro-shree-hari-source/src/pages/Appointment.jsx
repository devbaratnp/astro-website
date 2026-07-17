import { useState, useEffect, useRef, useCallback } from 'react';
import { CalendarBlank, WhatsappLogo, EnvelopeSimple, Clock, LockKey } from '@phosphor-icons/react';
import { PHONE, EMAIL, services } from '../constants';
import { createAppointment, getAvailableSlots } from '../services/api';
import { toN, bsMonths, bs2ad, ad2bs, getMaxDay, BS_MIN_YEAR, BS_MAX_YEAR } from '../utils/bsDate';

const yearOptions = [];
for (let y = BS_MIN_YEAR; y <= BS_MAX_YEAR; y++) yearOptions.push(y);

const hourOptions = [];
for (let h = 1; h <= 12; h++) hourOptions.push(h);

const minuteOptions = [];
for (let m = 0; m < 60; m++) minuteOptions.push(m);

const orderOptions = [
  { value: 'पहिलो', label: 'पहिलो' },
  { value: 'दोस्रो', label: 'दोस्रो' },
  { value: 'तेस्रो', label: 'तेस्रो' },
  { value: 'चौथो', label: 'चौथो' },
  { value: 'पाँचौं', label: 'पाँचौं' },
];

const genderOptions = [
  { value: 'छोरा', label: 'छोरा (Son)' },
  { value: 'छोरी', label: 'छोरी (Daughter)' },
];

export function Appointment() {
  const [sent, setSent] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');
  const [slots, setSlots] = useState([]);
  const [meetingUrl, setMeetingUrl] = useState('');
  const todayBS = useRef(ad2bs(new Date()));
  const [bsYear, setBsYear] = useState(todayBS.current.y);
  const [bsMonth, setBsMonth] = useState(todayBS.current.m);
  const [bsDay, setBsDay] = useState(todayBS.current.d);
  const [maxDay, setMaxDay] = useState(() => getMaxDay(todayBS.current.y, todayBS.current.m));
  const [amPm, setAmPm] = useState('am');
  const [timeHour, setTimeHour] = useState(12);
  const [timeMinute, setTimeMinute] = useState(0);

  useEffect(() => {
    const md = getMaxDay(bsYear, bsMonth);
    setMaxDay(md);
    if (bsDay > md) setBsDay(md);
  }, [bsYear, bsMonth, bsDay]);

  const handleYearChange = useCallback((e) => {
    const v = +e.target.value;
    setBsYear(v);
  }, []);

  const handleMonthChange = useCallback((e) => {
    setBsMonth(+e.target.value);
  }, []);

  const handleDayChange = useCallback((e) => {
    setBsDay(+e.target.value);
  }, []);

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
        birth_date: data.get('birth_date'),
        birth_time: data.get('birth_time'),
        birth_place: data.get('birth_place'),
        nwaran_name: data.get('nwaran_name'),
        father_name: data.get('father_name'),
        mother_name: data.get('mother_name'),
        birth_order: data.get('birth_order'),
        birth_gender: data.get('birth_gender'),
      });
      setMeetingUrl(response.data.meeting_url || '');
    } catch (error) {
      setError(error.message || 'अनुरोध सुरक्षित गर्न सकिएन। कृपया फेरि प्रयास गर्नुहोस्।');
      setSubmitting(false);
      return;
    }
    const bsDateStr = `${toN(bsYear)} ${bsMonths[bsMonth - 1]} ${toN(bsDay)}`;
    const adDate = bs2ad(bsYear, bsMonth, bsDay);
    const adStr = `${adDate.getFullYear()}-${String(adDate.getMonth() + 1).padStart(2, '0')}-${String(adDate.getDate()).padStart(2, '0')}`;
    const timeStr = `${toN(timeHour)}:${toN(String(timeMinute).padStart(2, '0'))} ${amPm === 'am' ? 'बिहान' : 'बेलुका'}`;
    const message = `नमस्कार गुरुज्यू, म ${data.get('name')} हुँ।\nफोन: ${data.get('phone')}\nसेवा: ${data.get('service')}\nजन्म: ${bsDateStr} (${adStr}) ${timeStr}\nस्थान: ${data.get('birth_place')}\nप्रश्न: ${data.get('message')}`;
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

            <div className="full section-divider">
              <span>जन्म विवरण</span>
            </div>

            <label className="full">जन्म मिति (वि.सं.) *
              <div className="bs-date-row">
                <span className="bs-date-field">
                  <small>वर्ष</small>
                  <select name="birth_bs_year" value={bsYear} onChange={handleYearChange} required>
                    {yearOptions.map(y => <option key={y} value={y}>{toN(y)}</option>)}
                  </select>
                </span>
                <span className="bs-date-field">
                  <small>महिना</small>
                  <select name="birth_bs_month" value={bsMonth} onChange={handleMonthChange} required>
                    {bsMonths.map((m, i) => <option key={i} value={i + 1}>{m}</option>)}
                  </select>
                </span>
                <span className="bs-date-field">
                  <small>गते</small>
                  <select name="birth_bs_day" value={bsDay} onChange={handleDayChange} required>
                    {Array.from({ length: maxDay }, (_, i) => <option key={i} value={i + 1}>{toN(i + 1)}</option>)}
                  </select>
                </span>
              </div>
            </label>

            <label className="full">जन्म समय *
              <div className="bs-time-row">
                <button type="button" className={`am-pm-btn ${amPm === 'am' ? 'active' : ''}`} onClick={() => setAmPm('am')}>बिहान (AM)</button>
                <button type="button" className={`am-pm-btn ${amPm === 'pm' ? 'active' : ''}`} onClick={() => setAmPm('pm')}>बेलुका (PM)</button>
                <input type="hidden" name="birth_am_pm" value={amPm} />
                <span className="bs-date-field" style={{ flex: '0 0 90px' }}>
                  <small>घण्टा</small>
                  <select name="birth_hour" value={timeHour} onChange={e => setTimeHour(+e.target.value)} required>
                    {hourOptions.map(h => <option key={h} value={h}>{toN(h)}</option>)}
                  </select>
                </span>
                <span style={{ padding: '0 4px', marginTop: '24px' }}>:</span>
                <span className="bs-date-field" style={{ flex: '0 0 90px' }}>
                  <small>मिनेट</small>
                  <select name="birth_minute" value={timeMinute} onChange={e => setTimeMinute(+e.target.value)} required>
                    {minuteOptions.map(m => <option key={m} value={m}>{toN(String(m).padStart(2, '0'))}</option>)}
                  </select>
                </span>
              </div>
            </label>

            <input type="hidden" name="birth_date" value={bs2ad(bsYear, bsMonth, bsDay).toISOString().slice(0, 10)} />
            <input type="hidden" name="birth_time" value={`${String(amPm === 'am' ? (timeHour === 12 ? 0 : timeHour) : (timeHour === 12 ? 12 : timeHour + 12)).padStart(2, '0')}:${String(timeMinute).padStart(2, '0')}:00`} />

            <label className="full">जन्मस्थान *
              <input name="birth_place" required placeholder="गाउँ / शहर / जिल्ला" />
            </label>

            <label>नाउँरणको नाम *
              <input name="nwaran_name" required placeholder="नाउँरण (न्वारण) नाम" />
            </label>

            <label>पिताको नाम *
              <input name="father_name" required placeholder="पिताको पूरा नाम" />
            </label>

            <label>माताको नाम *
              <input name="mother_name" required placeholder="माताको पूरा नाम" />
            </label>

            <label className="full">सन्तान क्रम *
              <div className="bs-order-row">
                <span className="bs-date-field">
                  <small>क्रम</small>
                  <select name="birth_order" required>
                    <option value="">-- क्रम --</option>
                    {orderOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </span>
                <span className="bs-date-field">
                  <small>लिङ्ग</small>
                  <select name="birth_gender" required>
                    <option value="">-- लिङ्ग --</option>
                    {genderOptions.map(g => <option key={g.value} value={g.value}>{g.label}</option>)}
                  </select>
                </span>
              </div>
            </label>

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
