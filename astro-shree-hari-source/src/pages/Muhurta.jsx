import { useState, useEffect } from 'react';
import { CheckCircle, XCircle, WarningCircle } from '@phosphor-icons/react';
import { toN, bsMonths, bs2ad, ad2bs, getMaxDay, BS_MIN_YEAR, BS_MAX_YEAR } from '../utils/bsDate';
import { getPanchangForDate, nakshatras } from '../utils/panchangEngine';

const muhurtaTypes = ['विवाह', 'गृहप्रवेश', 'व्रतबन्ध', 'व्यवसाय', 'यात्रा'];
const yearOptions = [];
for (let y = BS_MIN_YEAR; y <= BS_MAX_YEAR; y++) yearOptions.push(y);

const muhurtaNakshatraMap = {
  'विवाह': { good: [2, 4, 6, 10, 11, 12, 13, 14, 15, 16, 20, 21, 23, 24, 25], avoid: [0, 3, 5, 7, 8, 17, 18, 19, 22, 26] },
  'गृहप्रवेश': { good: [1, 2, 4, 6, 9, 10, 11, 12, 13, 14, 15, 20, 21, 23, 24, 25], avoid: [0, 3, 5, 7, 8, 16, 17, 18, 19, 22, 26] },
  'व्रतबन्ध': { good: [1, 2, 4, 6, 9, 10, 11, 12, 13, 14, 15, 20, 21, 23, 24, 25], avoid: [0, 3, 5, 7, 8, 16, 17, 18, 19, 22, 26] },
  'व्यवसाय': { good: [1, 2, 4, 6, 9, 10, 11, 12, 13, 14, 15, 20, 21, 23, 24, 25], avoid: [0, 3, 5, 7, 8, 16, 17, 18, 19, 22, 26] },
  'यात्रा': { good: [1, 2, 4, 6, 9, 10, 11, 12, 13, 14, 15, 20, 21, 23, 24, 25], avoid: [0, 3, 5, 7, 8, 16, 17, 18, 19, 22, 26] },
};

const goodDays = [false, true, false, true, true, true, false];
const dayNames = ['आइतबार', 'सोमबार', 'मङ्गलबार', 'बुधबार', 'बिहीबार', 'शुक्रबार', 'शनिबार'];

function getVerdict(type, nakshatraIndex, dayOfWeek) {
  const cfg = muhurtaNakshatraMap[type];
  const isGood = cfg.good.includes(nakshatraIndex);
  const isAvoid = cfg.avoid.includes(nakshatraIndex);
  const isGoodDay = goodDays[dayOfWeek];

  if (isGood && isGoodDay) return { verdict: 'शुभ', color: '#0b8d4e', description: 'यस दिनको नक्षत्र र वार दुवै शुभ छन्। ' + type + ' का लागि उत्तम मुहूर्त।' };
  if (isGood) return { verdict: 'मध्यम', color: '#d4a02b', description: 'नक्षत्र शुभ भए पनि वार शुभ छैन। आवश्यक भए मात्र गर्नुहोस्।' };
  if (isGoodDay) return { verdict: 'मध्यम', color: '#d4a02b', description: 'वार शुभ भए पनि नक्षत्र शुभ छैन। वैकल्पिक मिति रोज्नु उचित हुन्छ।' };
  return { verdict: 'अशुभ', color: '#b34a3a', description: 'यस दिन ' + type + ' का लागि मुहूर्त शुभ छैन। अर्को शुभ मिति हेर्नुहोस्।' };
}

export function Muhurta() {
  const [type, setType] = useState('विवाह');
  const todayBS = useState(() => ad2bs(new Date()))[0];
  const [bsYear, setBsYear] = useState(todayBS.y);
  const [bsMonth, setBsMonth] = useState(todayBS.m);
  const [bsDay, setBsDay] = useState(todayBS.d);
  const [maxDay, setMaxDay] = useState(() => getMaxDay(todayBS.y, todayBS.m));
  const [result, setResult] = useState(null);

  useEffect(() => {
    const md = getMaxDay(bsYear, bsMonth);
    setMaxDay(md);
    if (bsDay > md) setBsDay(md);
  }, [bsYear, bsMonth, bsDay]);

  function check(e) {
    e.preventDefault();
    const adDate = bs2ad(bsYear, bsMonth, bsDay);
    const panchang = getPanchangForDate(adDate);
    const dayOfWeek = adDate.getDay();
    const v = getVerdict(type, panchang.nakshatraIndex, dayOfWeek);
    setResult({
      ...v,
      bsDate: `${toN(bsYear)} ${bsMonths[bsMonth - 1]} ${toN(bsDay)}`,
      adDate: adDate.toISOString().slice(0, 10),
      day: dayNames[dayOfWeek],
      nakshatra: panchang.nakshatra,
      nakshatraIndex: panchang.nakshatraIndex + 1,
      tithi: panchang.tithi,
      paksha: panchang.paksha,
      moonRashi: panchang.moonRashi,
      type,
    });
  }

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
          <button className="button button-maroon">मुहूर्त हेर्नुहोस्</button>
        </form>

        {result && (
          <div className={`muhurta-result verdict-${result.verdict}`}>
            <div className="verdict-head">
              {result.verdict === 'शुभ' ? <CheckCircle size={48} color={result.color} weight="fill" /> :
               result.verdict === 'अशुभ' ? <XCircle size={48} color={result.color} weight="fill" /> :
               <WarningCircle size={48} color={result.color} weight="fill" />}
              <div>
                <strong style={{ color: result.color }}>{result.verdict}</strong>
                <span>{result.description}</span>
              </div>
            </div>
            <div className="verdict-details">
              <div><span>मिति</span><strong>{result.bsDate}</strong></div>
              <div><span>वार</span><strong>{result.day}</strong></div>
              <div><span>नक्षत्र</span><strong>{result.nakshatra} ({result.nakshatraIndex})</strong></div>
              <div><span>तिथि</span><strong>{result.paksha} {result.tithi}</strong></div>
              <div><span>चन्द्र राशि</span><strong>{result.moonRashi}</strong></div>
              <div><span>कार्य</span><strong>{result.type}</strong></div>
            </div>
            <p className="disclaimer">यो सामान्य मुहूर्त जानकारी हो; व्यक्तिगत कुण्डली अनुसार विस्तृत परामर्शका लागि गुरुज्यूसँग सम्पर्क गर्नुहोस्।</p>
          </div>
        )}
      </div>
    </section>
  );
}