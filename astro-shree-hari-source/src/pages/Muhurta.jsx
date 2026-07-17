import { useState } from 'react';
import { CalendarBlank, Compass, CheckCircle, XCircle, WarningCircle } from '@phosphor-icons/react';

const muhurtaTypes = ['विवाह', 'गृहप्रवेश', 'व्रतबन्ध', 'व्यवसाय', 'यात्रा'];

export function Muhurta() {
  const [type, setType] = useState('विवाह');
  const [date, setDate] = useState(new Date().toISOString().slice(0, 10));
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);

  function check(e) {
    e.preventDefault();
    setLoading(true);
    fetch(`/backend/api/muhurta.php?type=${encodeURIComponent(type)}&date=${date}`)
      .then(r => r.json())
      .then(d => { if (d.success) setResult(d.data); })
      .catch(() => setResult(null))
      .finally(() => setLoading(false));
  }

  const verdictIcon = result?.verdict === 'शुभ' ? CheckCircle : result?.verdict === 'अशुभ' ? XCircle : WarningCircle;
  const verdictColor = result?.verdict === 'शुभ' ? '#0b8d4e' : result?.verdict === 'अशुभ' ? '#b34a3a' : '#d4a02b';

  return (
    <section className="section page-section">
      <div className="container">
        <div className="section-heading">
          <span>शुभ मुहूर्त</span>
          <h2>मुहूर्त परीक्षण</h2>
          <p>विवाह, गृहप्रवेश, व्रतबन्ध, व्यवसाय र यात्राका लागि शुभ समय जाँच गर्नुहोस्</p>
        </div>

        <form className="muhurta-form" onSubmit={check}>
          <div className="muhurta-fields">
            <label>कार्य चयन
              <select value={type} onChange={e => setType(e.target.value)}>
                {muhurtaTypes.map(t => <option key={t}>{t}</option>)}
              </select>
            </label>
            <label>मिति
              <input type="date" value={date} onChange={e => setDate(e.target.value)} />
            </label>
          </div>
          <button className="button button-maroon" disabled={loading}>{loading ? 'जाँच हुँदैछ…' : 'मुहूर्त हेर्नुहोस्'}</button>
        </form>

        {result && (
          <div className={`muhurta-result verdict-${result.verdict}`}>
            <div className="verdict-head">
              {verdictIcon({ size: 48, color: verdictColor, weight: 'fill' })}
              <div>
                <strong style={{ color: verdictColor }}>{result.verdict}</strong>
                <span>{result.description}</span>
              </div>
            </div>
            <div className="verdict-details">
              <div><span>मिति</span><strong>{result.date}</strong></div>
              <div><span>वार</span><strong>{result.day}</strong></div>
              <div><span>नक्षत्र</span><strong>{result.nakshatra}</strong></div>
              <div><span>कार्य</span><strong>{result.type}</strong></div>
            </div>
            <p className="disclaimer">{result.disclaimer}</p>
          </div>
        )}
      </div>
    </section>
  );
}
