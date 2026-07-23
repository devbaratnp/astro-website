(function () {
  var BS_DATA = {
    1970:[31,31,32,32,31,30,30,29,30,29,30,30],1971:[31,32,31,32,31,30,30,30,29,29,30,31],
    1972:[30,32,31,32,31,30,30,30,29,30,29,31],1973:[31,31,32,31,31,31,30,29,30,29,30,30],
    1974:[31,31,32,32,31,30,30,29,30,29,30,30],1975:[31,32,31,32,31,30,30,30,29,29,30,31],
    1976:[30,32,31,32,31,30,30,30,29,30,29,31],1977:[31,31,32,31,31,31,30,29,30,29,30,30],
    1978:[31,31,32,32,31,30,30,29,30,29,30,30],1979:[31,32,31,32,31,30,30,30,29,29,30,31],
    1980:[31,31,31,32,31,31,29,30,29,30,29,31],1981:[31,31,32,31,31,31,30,29,30,29,30,30],
    1982:[31,31,32,32,31,30,30,29,30,29,30,30],1983:[31,32,31,32,31,30,30,30,29,29,30,31],
    1984:[31,31,31,32,31,31,29,30,30,29,30,30],1985:[31,31,32,31,31,31,30,29,30,29,30,30],
    1986:[31,31,32,32,31,30,30,29,30,29,30,30],1987:[31,32,31,32,31,30,30,30,29,29,30,31],
    1988:[31,31,31,32,31,31,29,30,30,29,30,30],1989:[31,31,32,31,31,31,30,29,30,29,30,30],
    1990:[31,32,31,32,31,30,30,29,30,29,30,30],1991:[31,32,31,32,31,30,30,30,29,30,29,31],
    1992:[31,31,31,32,31,31,30,29,30,29,30,30],1993:[31,31,32,31,31,31,30,29,30,29,30,30],
    1994:[31,32,31,32,31,30,30,30,29,29,30,30],1995:[31,32,31,32,31,30,30,30,29,30,29,31],
    1996:[31,31,32,31,31,31,30,29,30,29,30,30],1997:[31,31,32,31,31,31,30,29,30,29,30,30],
    1998:[31,32,31,32,31,30,30,30,29,29,30,30],1999:[31,32,31,32,31,30,30,30,29,30,29,31],
    2000:[31,31,32,31,31,31,30,29,30,29,30,30],2001:[31,31,32,32,31,30,30,29,30,29,30,30],
    2002:[31,32,31,32,31,30,30,30,29,29,30,31],2003:[30,32,31,32,31,30,30,30,29,30,29,31],
    2004:[31,31,32,31,31,31,30,29,30,29,30,30],2005:[31,31,32,32,31,30,30,29,30,29,30,30],
    2006:[31,32,31,32,31,30,30,30,29,29,30,31],2007:[31,31,31,32,31,31,29,30,30,29,29,31],
    2008:[31,31,32,31,31,31,30,29,30,29,30,30],2009:[31,31,32,32,31,30,30,29,30,29,30,30],
    2010:[31,32,31,32,31,30,30,30,29,29,30,31],2011:[31,31,31,32,31,31,29,30,30,29,30,30],
    2012:[31,31,32,31,31,31,30,29,30,29,30,30],2013:[31,31,32,32,31,30,30,29,30,29,30,30],
    2014:[31,32,31,32,31,30,30,30,29,29,30,31],2015:[31,31,31,32,31,31,29,30,30,29,30,30],
    2016:[31,31,32,31,31,31,30,29,30,29,30,30],2017:[31,32,31,32,31,30,30,29,30,29,30,30],
    2018:[31,32,31,32,31,30,30,30,29,30,29,31],2019:[31,31,31,32,31,31,30,29,30,29,30,30],
    2020:[31,31,32,31,31,31,30,29,30,29,30,30],2021:[31,32,31,32,31,30,30,30,29,29,30,30],
    2022:[31,32,31,32,31,30,30,30,29,30,29,31],2023:[31,31,31,32,31,31,30,29,30,29,30,30],
    2024:[31,31,32,31,31,31,30,29,30,29,30,30],2025:[31,32,31,32,31,30,30,30,29,29,30,31],
    2026:[30,32,31,32,31,30,30,30,29,30,29,31],2027:[31,31,32,31,31,31,30,29,30,29,30,30],
    2028:[31,31,32,31,32,30,30,29,30,29,30,30],2029:[31,32,31,32,31,30,30,30,29,29,30,31],
    2030:[30,32,31,32,31,30,30,30,29,30,29,31],2031:[31,31,32,31,31,31,30,29,30,29,30,30],
    2032:[31,31,32,32,31,30,30,29,30,29,30,30],2033:[31,32,31,32,31,30,30,30,29,29,30,31],
    2034:[30,32,31,32,31,31,29,30,30,29,29,31],2035:[31,31,32,31,31,31,30,29,30,29,30,30],
    2036:[31,31,32,32,31,30,30,29,30,29,30,30],2037:[31,32,31,32,31,30,30,30,29,29,30,31],
    2038:[31,31,31,32,31,31,29,30,30,29,30,30],2039:[31,31,32,31,31,31,30,29,30,29,30,30],
    2040:[31,31,32,32,31,30,30,29,30,29,30,30],2041:[31,32,31,32,31,30,30,30,29,29,30,31],
    2042:[31,31,31,32,31,31,29,30,30,29,30,30],2043:[31,31,32,31,31,31,30,29,30,29,30,30],
    2044:[31,32,31,32,31,30,30,29,30,29,30,30],2045:[31,32,31,32,31,30,30,30,29,29,30,31],
    2046:[31,31,31,32,31,31,30,29,30,29,30,30],2047:[31,31,32,31,31,31,30,29,30,29,30,30],
    2048:[31,32,31,32,31,30,30,30,29,29,30,30],2049:[31,32,31,32,31,30,30,30,29,30,29,31],
    2050:[31,31,31,32,31,31,30,29,30,29,30,30],2051:[31,31,32,31,31,31,30,29,30,29,30,30],
    2052:[31,32,31,32,31,30,30,30,29,29,30,30],2053:[31,32,31,32,31,30,30,30,29,30,29,31],
    2054:[31,31,32,31,31,31,30,29,30,29,30,30],2055:[31,31,32,31,32,30,30,29,30,29,30,30],
    2056:[31,32,31,32,31,30,30,30,29,29,30,31],2057:[30,32,31,32,31,30,30,30,29,30,29,31],
    2058:[31,31,32,31,31,31,30,29,30,29,30,30],2059:[31,31,32,32,31,30,30,29,30,29,30,30],
    2060:[31,32,31,32,31,30,30,30,29,29,30,31],2061:[31,31,31,32,31,31,29,30,29,30,29,31],
    2062:[31,31,32,31,31,31,30,29,30,29,30,30],2063:[31,31,32,32,31,30,30,29,30,29,30,30],
    2064:[31,32,31,32,31,30,30,30,29,29,30,31],2065:[31,31,31,32,31,31,29,30,30,29,29,31],
    2066:[31,31,32,31,31,31,30,29,30,29,30,30],2067:[31,31,32,32,31,30,30,29,30,29,30,30],
    2068:[31,32,31,32,31,30,30,30,29,29,30,31],2069:[31,31,31,32,31,31,29,30,30,29,30,30],
    2070:[31,31,32,31,31,31,30,29,30,29,30,30],2071:[31,32,31,32,31,30,30,29,30,29,30,30],
    2072:[31,32,31,32,31,30,30,30,29,29,30,31],2073:[31,31,31,32,31,31,30,29,30,29,30,30],
    2074:[31,31,32,31,31,31,30,29,30,29,30,30],2075:[31,32,31,32,31,30,30,30,29,29,30,30],
    2076:[31,32,31,32,31,30,30,30,29,30,29,31],2077:[31,31,31,32,31,31,30,29,30,29,30,30],
    2078:[31,31,32,31,31,31,30,29,30,29,30,30],2079:[31,32,31,32,31,30,30,30,29,29,30,30],
    2080:[31,32,31,32,31,30,30,30,29,30,29,31],2081:[31,31,32,31,31,31,30,29,30,29,30,30],
    2082:[31,31,32,31,31,31,30,29,30,29,30,30],2083:[31,32,31,32,31,30,30,30,29,29,30,31],
    2084:[30,32,31,32,31,30,30,30,29,30,29,31],2085:[31,31,32,31,31,31,30,29,30,29,30,30],
    2086:[31,31,32,32,31,30,30,29,30,29,30,30],2087:[31,32,31,32,31,30,30,30,29,29,30,31],
    2088:[30,32,31,32,31,31,29,30,29,30,29,31],2089:[31,31,32,31,31,31,30,29,30,29,30,30],
    2090:[31,31,32,32,31,30,30,29,30,29,30,30]
  };

  var BS_START = 1975;
  var BASE_AD = new Date(1918, 3, 13);

  var bsMonths = ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];

  var nakshatras = [
    'अश्विनी','भरणी','कृत्तिका','रोहिणी','मृगशिरा','आर्द्रा',
    'पुनर्वसु','पुष्य','अश्लेषा','मघा','पूर्वाफाल्गुनी','उत्तराफाल्गुनी',
    'हस्त','चित्रा','स्वाती','विशाखा','अनुराधा','ज्येष्ठा',
    'मूल','पूर्वाषाढा','उत्तराषाढा','श्रवण','धनिष्ठा','शतभिषा',
    'पूर्वभाद्रपद','उत्तरभाद्रपद','रेवती'
  ];

  var tithis = [
    'प्रतिपदा','द्वितीया','तृतीया','चतुर्थी','पञ्चमी','षष्ठी',
    'सप्तमी','अष्टमी','नवमी','दशमी','एकादशी','द्वादशी',
    'त्रयोदशी','चतुर्दशी','पूर्णिमा','प्रतिपदा','द्वितीया','तृतीया',
    'चतुर्थी','पञ्चमी','षष्ठी','सप्तमी','अष्टमी','नवमी',
    'दशमी','एकादशी','द्वादशी','त्रयोदशी','चतुर्दशी','अमावास्या'
  ];

  var rashis = [
    'मेष','वृष','मिथुन','कर्कट','सिंह','कन्या',
    'तुला','वृश्चिक','धनु','मकर','कुम्भ','मीन'
  ];

  var muhurtaNakshatraMap = {
    'विवाह': { good: [2,4,6,10,11,12,13,14,15,16,20,21,23,24,25], avoid: [0,3,5,7,8,17,18,19,22,26] },
    'गृहप्रवेश': { good: [1,2,4,6,9,10,11,12,13,14,15,20,21,23,24,25], avoid: [0,3,5,7,8,16,17,18,19,22,26] },
    'व्रतबन्ध': { good: [1,2,4,6,9,10,11,12,13,14,15,20,21,23,24,25], avoid: [0,3,5,7,8,16,17,18,19,22,26] },
    'व्यवसाय': { good: [1,2,4,6,9,10,11,12,13,14,15,20,21,23,24,25], avoid: [0,3,5,7,8,16,17,18,19,22,26] },
    'यात्रा': { good: [1,2,4,6,9,10,11,12,13,14,15,20,21,23,24,25], avoid: [0,3,5,7,8,16,17,18,19,22,26] }
  };

  var goodDays = [false, true, false, true, true, true, false];
  var dayNames = ['आइतबार','सोमबार','मङ्गलबार','बुधबार','बिहीबार','शुक्रबार','शनिबार'];

  function toN(num) {
    if (num === undefined || num === null) return '';
    return String(num).replace(/[0-9]/g, function (d) { return '०१२३४५६७८९'[d]; });
  }

  function bsMonthDays(year, idx0) {
    if (BS_DATA[year]) return BS_DATA[year][idx0];
    return 30;
  }

  function bsYearDays(year) {
    if (BS_DATA[year]) {
      var s = 0;
      for (var j = 0; j < 12; j++) s += BS_DATA[year][j];
      return s;
    }
    return 365;
  }

  function bs2ad(y, m, d) {
    var t = 0;
    if (y >= BS_START) {
      for (var i = BS_START; i < y; i++) t += bsYearDays(i);
    } else {
      for (var i = y; i < BS_START; i++) t -= bsYearDays(i);
    }
    for (var j = 0; j < m - 1; j++) t += bsMonthDays(y, j);
    t += d - 1;
    var r = new Date(BASE_AD);
    r.setDate(r.getDate() + t);
    return r;
  }

  function getMaxDay(year, month) {
    return bsMonthDays(year, month - 1);
  }

  function getPanchangForDate(date) {
    var ref = new Date(2000, 0, 1);
    var days = (date - ref) / 86400000;
    var nakIndex = Math.floor(((days * 27 / 27.32166) % 27 + 27) % 27);
    var tithiIndex = Math.floor(((days * 30 / 29.53059) % 30 + 30) % 30);
    var moonRashiIndex = Math.floor(nakIndex / 2.25) % 12;
    var paksha = tithiIndex < 15 ? 'शुक्ल' : 'कृष्ण';
    return {
      tithi: tithis[tithiIndex],
      tithiIndex: tithiIndex,
      nakshatra: nakshatras[nakIndex],
      nakshatraIndex: nakIndex,
      moonRashi: rashis[moonRashiIndex],
      moonRashiIndex: moonRashiIndex,
      paksha: paksha
    };
  }

  function getVerdict(type, nakshatraIndex, dayOfWeek) {
    var cfg = muhurtaNakshatraMap[type];
    var isGood = cfg.good.indexOf(nakshatraIndex) !== -1;
    var isAvoid = cfg.avoid.indexOf(nakshatraIndex) !== -1;
    var isGoodDay = goodDays[dayOfWeek];
    if (isGood && isGoodDay) return { verdict: 'शुभ', color: '#0b8d4e', desc: 'यस दिनको नक्षत्र र वार दुवै शुभ छन्। ' + type + ' का लागि उत्तम मुहूर्त।' };
    if (isGood) return { verdict: 'मध्यम', color: '#d4a02b', desc: 'नक्षत्र शुभ भए पनि वार शुभ छैन। आवश्यक भए मात्र गर्नुहोस्।' };
    if (isGoodDay) return { verdict: 'मध्यम', color: '#d4a02b', desc: 'वार शुभ भए पनि नक्षत्र शुभ छैन। वैकल्पिक मिति रोज्नु उचित हुन्छ।' };
    return { verdict: 'अशुभ', color: '#b34a3a', desc: 'यस दिन ' + type + ' का लागि मुहूर्त शुभ छैन। अर्को शुभ मिति हेर्नुहोस्।' };
  }

  function popSelect(sel, vals, fn) {
    sel.innerHTML = '';
    for (var i = 0; i < vals.length; i++) {
      var opt = document.createElement('option');
      opt.value = vals[i];
      opt.textContent = fn ? fn(vals[i]) : vals[i];
      sel.appendChild(opt);
    }
  }

  function updateMaxDay() {
    var y = parseInt(document.getElementById('bs-year').value, 10);
    var m = parseInt(document.getElementById('bs-month').value, 10);
    var maxD = getMaxDay(y, m);
    var daySel = document.getElementById('bs-day');
    var cur = parseInt(daySel.value, 10);
    popSelect(daySel, range(1, maxD), function (v) { return toN(v); });
    if (cur <= maxD) daySel.value = cur;
  }

  function range(start, end) {
    var a = [];
    for (var i = start; i <= end; i++) a.push(i);
    return a;
  }

  function init() {
    var yearSel = document.getElementById('bs-year');
    var monthSel = document.getElementById('bs-month');
    var daySel = document.getElementById('bs-day');

    popSelect(yearSel, range(1970, 2090), function (v) { return toN(v); });
    popSelect(monthSel, range(1, 12), function (v) { return bsMonths[v - 1]; });
    popSelect(daySel, range(1, 31), function (v) { return toN(v); });

    var todayBS = (function () {
      var dt = new Date();
      var tgt = new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
      var df = Math.round((tgt - BASE_AD) / 864e5);
      var y = BS_START;
      if (df >= 0) {
        while (true) {
          var yd = bsYearDays(y);
          if (df < yd) break;
          df -= yd;
          y++;
          if (y > 2100) break;
        }
      } else {
        while (df < 0) {
          y--;
          df += bsYearDays(y);
          if (y < 1900) break;
        }
      }
      var m = 1;
      for (var i = 0; i < 12; i++) {
        var md = bsMonthDays(y, i);
        if (df < md) { m = i + 1; break; }
        df -= md;
      }
      return { y: y, m: m, d: df + 1 };
    })();

    yearSel.value = todayBS.y;
    monthSel.value = todayBS.m;
    updateMaxDay();
    daySel.value = Math.min(todayBS.d, parseInt(daySel.options[daySel.options.length - 1].value, 10));

    yearSel.addEventListener('change', updateMaxDay);
    monthSel.addEventListener('change', updateMaxDay);

    document.getElementById('muhurta-form').addEventListener('submit', function (e) {
      e.preventDefault();
      var type = document.getElementById('muhurta-type').value;
      var bsY = parseInt(yearSel.value, 10);
      var bsM = parseInt(monthSel.value, 10);
      var bsD = parseInt(daySel.value, 10);
      var adDate = bs2ad(bsY, bsM, bsD);
      var panchang = getPanchangForDate(adDate);
      var dayOfWeek = adDate.getDay();
      var v = getVerdict(type, panchang.nakshatraIndex, dayOfWeek);

      var iconSvg = '';
      if (v.verdict === 'शुभ') iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="' + v.color + '" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg>';
      else if (v.verdict === 'अशुभ') iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="' + v.color + '" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm37.66,130.34a8,8,0,0,1-11.32,11.32L128,139.31l-26.34,26.35a8,8,0,0,1-11.32-11.32L116.69,128,90.34,101.66a8,8,0,0,1,11.32-11.32L128,116.69l26.34-26.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/></svg>';
      else iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="' + v.color + '" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm-8,56a8,8,0,0,1,16,0v56a8,8,0,0,1-16,0Zm8,104a12,12,0,1,1,12-12A12,12,0,0,1,128,184Z"/></svg>';

      document.getElementById('verdict-icon').innerHTML = iconSvg;
      document.getElementById('verdict-text').textContent = v.verdict;
      document.getElementById('verdict-text').style.color = v.color;
      document.getElementById('verdict-desc').textContent = v.desc;

      document.getElementById('detail-date').textContent = toN(bsY) + ' ' + bsMonths[bsM - 1] + ' ' + toN(bsD);
      document.getElementById('detail-day').textContent = dayNames[dayOfWeek];
      document.getElementById('detail-nakshatra').textContent = panchang.nakshatra + ' (' + toN(panchang.nakshatraIndex + 1) + ')';
      document.getElementById('detail-tithi').textContent = panchang.paksha + ' ' + panchang.tithi;
      document.getElementById('detail-moon-rashi').textContent = panchang.moonRashi;
      document.getElementById('detail-type').textContent = type;

      document.getElementById('muhurta-result').style.display = 'block';
      document.getElementById('muhurta-result').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
