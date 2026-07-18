import { useState, useEffect } from 'react';
import { CalendarBlank, Compass, CheckCircle, XCircle, WarningCircle } from '@phosphor-icons/react';
import { toN, bsMonths, bs2ad, ad2bs, getMaxDay, BS_MIN_YEAR, BS_MAX_YEAR } from '../utils/bsDate';

const muhurtaTypes = ['विवाह', 'गृहप्रवेश', 'व्रतबन्ध', 'व्यवसाय', 'यात्रा'];
const yearOptions = [];
for (let y = BS_MIN_YEAR; y <= BS_MAX_YEAR; y++) yearOptions.push(y);

export function Muhurta() {
  const [type, setType] = useState('विवाह');
  const todayBS = useState(() => ad2bs(new Date()))[0];
  const [bsYear, setBsYear] = useState(todayBS.y);
  const [bsMonth, setBsMonth] = useState(todayBS.m);
  const [bsDay, setBsDay] = useState(todayBS.d);
  const [maxDay, setMaxDay] = useState(() => getMaxDay(todayBS.y, todayBS.m));
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const md = getMaxDay(bsYear, bsMonth);
    setMaxDay(md);
    if (bsDay > md) setBsDay(md);
  }, [bsYear, bsMonth, bsDay]);

  function check(e) {
    e.preventDefault();
    setLoading(true);
    const adDate = bs2ad(bsYear, bsMonth, bsDay);
    const dateStr = adDate.toISOString().slice(0, 10);
    fetch(`/backend/api/muhurta.php?type=${encodeURIComponent(type)}&date=${dateStr}`)
      .then(r => r.json())
      .then(d => { if (d.success) setResult({ ...d.data, bsDate: `${toN(bsYear)} ${bsMonths[bsMonth - 1]} ${toN(bsDay)}` }); })
      .catch(() => setResult(null))
      .finally(() => setLoading(false));
  }

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

            <div className="section-divider"><span>मिति (नेपाली)</span></div>

            <div className="bs-date-row">
              <div className="bs-date-field">
                <small>वर्ष</small>
                <select value={bsYear} onChange={e => setBsYear(Number(e.target.value))}>
                  {yearOptions.map(y => <option key={y} value={y}>{toN(y)}</option>)}
                </select>
              </div>
              <div className="bs-date-field">
                <small>महिना</small>
                <select value={bsMonth} onChange={e => setBsMonth(Number(e.target.value))}>
                  {bsMonths.map((m, i) => <option key={i} value={i + 1}>{m}</option>)}
                </select>
              </div>
              <div className="bs-date-field">
                <small>गते</small>
                <select value={bsDay} onChange={e => setBsDay(Number(e.target.value))}>
                  {Array.from({ length: maxDay }, (_, i) => <option key={i} value={i + 1}>{toN(i + 1)}</option>)}
                </select>
              </div>
            </div>
          </div>
          <button className="button button-maroon" disabled={loading}>{loading ? 'जाँच हुँदैछ…' : 'मुहूर्त हेर्नुहोस्'}</button>
        </form>

        {result && (
          <div className={`muhurta-result verdict-${result.verdict}`}>
            <div className="verdict-head">
              {result.verdict === 'शुभ' ? <CheckCircle size={48} color={verdictColor} weight="fill" /> :
               result.verdict === 'अशुभ' ? <XCircle size={48} color={verdictColor} weight="fill" /> :
               <WarningCircle size={48} color={verdictColor} weight="fill" />}
              <div>
                <strong style={{ color: verdictColor }}>{result.verdict}</strong>
                <span>{result.description}</span>
              </div>
            </div>
            <div className="verdict-details">
              <div><span>मिति</span><strong>{result.bsDate}</strong></div>
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