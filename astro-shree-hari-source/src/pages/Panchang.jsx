import { useEffect, useState } from 'react';
import { getPanchang, getHoroscope } from '../services/api';
import { buildForecastEntries, buildPanchangRows } from '../utils/panchangDisplay';

const ne = {
  panchang: '\u092a\u091e\u094d\u091a\u093e\u0919\u094d\u0917',
  day: '\u0926\u093f\u0928 \u092b\u0932',
  night: '\u0930\u093e\u0924\u094d\u0930\u0940 \u092b\u0932',
  today: '\u0906\u091c\u0915\u094b \u092a\u091e\u094d\u091a\u093e\u0919\u094d\u0917',
  dailyUpdate: '\u0926\u0948\u0928\u093f\u0915 \u0905\u092a\u0921\u0947\u091f',
  tithi: '\u0924\u093f\u0925\u093f',
  nakshatra: '\u0928\u0915\u094d\u0937\u0924\u094d\u0930',
  sunrise: '\u0938\u0942\u0930\u094d\u092f\u094b\u0926\u092f',
  sunset: '\u0938\u0942\u0930\u094d\u092f\u093e\u0938\u094d\u0924',
  previousDay: '\u0905\u0918\u093f\u0932\u094d\u0932\u094b \u0926\u093f\u0928',
  nextDay: '\u0905\u0930\u094d\u0915\u094b \u0926\u093f\u0928',
  todayLabel: '\u0906\u091c\u0915\u094b \u0926\u093f\u0928',
  loading: '\u0932\u094b\u0921 \u0939\u0941\u0901\u0926\u0948\u091b\u2026',
  unavailable: '\u092a\u091e\u094d\u091a\u093e\u0919\u094d\u0917 \u0935\u093f\u0935\u0930\u0923 \u0905\u0939\u093f\u0932\u0947 \u0909\u092a\u0932\u092c\u094d\u0927 \u091b\u0948\u0928\u0964 \u0915\u0943\u092a\u092f\u093e \u092b\u0947\u0930\u093f \u092a\u094d\u0930\u092f\u093e\u0938 \u0917\u0930\u094d\u0928\u0941\u0939\u094b\u0938\u094d\u0964',
  notice: '\u0935\u093f\u0935\u0930\u0923 \u092a\u094d\u0930\u0936\u093e\u0938\u0928\u093f\u0915 \u0930 \u0917\u0923\u0928\u093e\u0924\u094d\u092e\u0915 \u0921\u093e\u091f\u093e\u092e\u093e \u0906\u0927\u093e\u0930\u093f\u0924 \u091b\u0964',
  horoscope: '\u0906\u091c\u0915\u094b \u0930\u093e\u0936\u093f\u092b\u0932',
  noForecast: '\u0935\u093f\u0935\u0930\u0923 \u0909\u092a\u0932\u092c\u094d\u0927 \u091b\u0948\u0928\u0964',
};
const tabs = [ne.panchang, ne.day, ne.night];
const toDisplayDate = (value) => new Intl.DateTimeFormat('ne-NP', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(`${value}T12:00:00`));

export function Panchang() {
  const [date, setDate] = useState(new Date().toISOString().slice(0, 10));
  const [panchang, setPanchang] = useState(null);
  const [horoscope, setHoroscope] = useState([]);
  const [activeTab, setActiveTab] = useState(tabs[0]);
  const [error, setError] = useState('');

  useEffect(() => {
    setError('');
    Promise.all([getPanchang(date), getHoroscope(date)])
      .then(([p, h]) => { setPanchang(p.panchang); setHoroscope(h.items || []); })
      .catch(() => setError(ne.unavailable));
  }, [date]);

  const changeDate = (days) => {
    const next = new Date(`${date}T12:00:00`);
    next.setDate(next.getDate() + days);
    setDate(next.toISOString().slice(0, 10));
  };
  const rows = buildPanchangRows(panchang);
  const forecastEntries = buildForecastEntries(horoscope, activeTab === ne.night ? 'night' : 'day');

  return <section className="section page-section panchang-page"><div className="container panchang-shell">
    <div className="panchang-date-nav"><button aria-label={ne.previousDay} onClick={() => changeDate(-1)}>&lsaquo;</button><label>{ne.today}<input type="date" value={date} onChange={(event) => setDate(event.target.value)} /></label><button aria-label={ne.nextDay} onClick={() => changeDate(1)}>&rsaquo;</button></div>
    <header className="panchang-hero"><aside className="panchang-calendar"><strong>{new Date(`${date}T12:00:00`).getDate()}</strong><b>{toDisplayDate(date)}</b><span>{ne.todayLabel}</span><i aria-hidden="true" /></aside><div><span className="panchang-kicker">{ne.dailyUpdate}</span><h1>{panchang?.special_events_ne || ne.today}</h1><div className="panchang-summary"><div>{ne.tithi}<strong>{panchang?.tithi || ne.loading}</strong></div><div>{ne.nakshatra}<strong>{panchang?.nakshatra || '-'}</strong></div><div>{ne.sunrise}<strong>{panchang?.sunrise || '-'}</strong></div><div>{ne.sunset}<strong>{panchang?.sunset || '-'}</strong></div></div></div></header>
    <div className="panchang-tabs" role="tablist">{tabs.map((tab) => <button key={tab} role="tab" aria-selected={activeTab === tab} className={activeTab === tab ? 'active' : ''} onClick={() => setActiveTab(tab)}>{tab}</button>)}</div>
    {error ? <p className="form-error">{error}</p> : <section className="panchang-detail-card"><h2>{activeTab}</h2><hr />{activeTab === ne.panchang ? <div className="panchang-facts">{rows.map(([label, value]) => <div key={label}><b>{label}</b><span>{value}</span></div>)}</div> : <div className="panchang-forecast-grid">{forecastEntries.length ? forecastEntries.map((entry) => <article className="panchang-forecast-card" key={entry.title}><h3>{entry.title}</h3><p>{entry.body}</p>{entry.note && <small>{entry.note}</small>}</article>) : <p className="panchang-placeholder">{ne.noForecast}</p>}</div>}</section>}
    <p className="panchang-notice">{ne.notice}</p>
    <h2 className="subheading">{ne.horoscope}</h2><div className="horoscope-grid">{horoscope.map((item) => <article key={item.zodiac_id} className="horoscope-card"><h3>{item.zodiac_ne}</h3><p>{item.moon_interpretation}</p></article>)}</div>
  </div></section>;
}