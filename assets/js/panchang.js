(function () {
  var dataEl = document.getElementById('panchang-data');
  var initialData = dataEl ? JSON.parse(dataEl.textContent) : { panchang: null, horoscope: { items: [] }, bs_initial: null };
  var bsMonthsEl = document.getElementById('panchang-bs-months');
  var bsMonths = bsMonthsEl ? JSON.parse(bsMonthsEl.textContent) : ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];
  var bsDataEl = document.getElementById('panchang-bs-data');
  var BS_DATA = bsDataEl ? JSON.parse(bsDataEl.textContent) : {};
  var BS_START = 1900;
  var BASE_AD = new Date(1918, 3, 13);

  var yearSel = document.getElementById('bs-year');
  var monthSel = document.getElementById('bs-month');
  var daySel = document.getElementById('bs-day');
  var prevBtn = document.getElementById('prev-day');
  var nextBtn = document.getElementById('next-day');
  var detailTitle = document.getElementById('detail-title');
  var tabPanchang = document.getElementById('tab-panchang');
  var tabDay = document.getElementById('tab-day');
  var tabNight = document.getElementById('tab-night');
  var dayGrid = document.getElementById('day-forecast-grid');
  var nightGrid = document.getElementById('night-forecast-grid');
  var horoGrid = document.getElementById('horoscope-grid');
  var summaryTithi = document.getElementById('summary-tithi');
  var summaryNakshatra = document.getElementById('summary-nakshatra');
  var summarySunrise = document.getElementById('summary-sunrise');
  var summarySunset = document.getElementById('summary-sunset');
  var tabBtns = document.querySelectorAll('.tab-btn');
  var eventsContainer = document.querySelector('.panchang-events');

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

  function ad2bs(dt) {
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
  }

  function formatBsDate(bs) {
    if (!bs) return '';
    return bsMonths[bs.m - 1] + ' ' + toN(bs.y);
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

  function range(start, end) {
    var a = [];
    for (var i = start; i <= end; i++) a.push(i);
    return a;
  }

  function getMaxDay(year, month) {
    return bsMonthDays(year, month - 1);
  }

  function updateMaxDay() {
    var y = parseInt(yearSel.value, 10);
    var m = parseInt(monthSel.value, 10);
    var maxD = getMaxDay(y, m);
    var cur = parseInt(daySel.value, 10);
    popSelect(daySel, range(1, maxD), toN);
    if (cur <= maxD) daySel.value = cur;
  }

  function getBsFromSelects() {
    return {
      y: parseInt(yearSel.value, 10),
      m: parseInt(monthSel.value, 10),
      d: parseInt(daySel.value, 10)
    };
  }

  function setSelectsFromBs(bs) {
    yearSel.value = bs.y;
    monthSel.value = bs.m;
    updateMaxDay();
    daySel.value = Math.min(bs.d, parseInt(daySel.options[daySel.options.length - 1].value, 10));
  }

  function updatePanchang(panchang) {
    summaryTithi.textContent = panchang && panchang.tithi ? panchang.tithi : 'लोड हुँदैछ…';
    summaryNakshatra.textContent = panchang && panchang.nakshatra ? panchang.nakshatra : '-';
    summarySunrise.textContent = panchang && panchang.sunrise ? panchang.sunrise : '-';
    summarySunset.textContent = panchang && panchang.sunset ? panchang.sunset : '-';
  }

  function updatePanchangFacts(panchang) {
    var rows = [];
    var labels = { 'तिथि': 'tithi', 'सूर्योदय': 'sunrise', 'सूर्यास्त': 'sunset', 'नक्षत्र': 'nakshatra', 'करण': 'karana', 'योग': 'yoga' };
    for (var label in labels) {
      var key = labels[label];
      if (panchang && panchang[key]) {
        rows.push('<div><b>' + label + '</b><span>' + panchang[key] + '</span></div>');
      }
    }
    tabPanchang.innerHTML = rows.length > 0
      ? '<div class="panchang-facts">' + rows.join('') + '</div>'
      : '<p class="panchang-placeholder">पञ्चाङ्ग विवरण अहिले उपलब्ध छैन।</p>';
  }

  function updateSpecialEvents(panchang) {
    if (!eventsContainer) return;
    var events = panchang && panchang.special_events ? panchang.special_events : [];
    if (events.length > 0) {
      var html = '';
      for (var i = 0; i < events.length; i++) {
        var text = events[i].ne || events[i].en || '';
        html += '<span class="panchang-event-tag">' + text + '</span>';
      }
      eventsContainer.innerHTML = html;
      eventsContainer.style.display = 'flex';
    } else {
      eventsContainer.style.display = 'none';
    }
  }

  function updateForecasts(items) {
    var dayCards = [];
    var nightCards = [];
    var horoCards = [];

    if (items && items.length) {
      items.forEach(function (it) {
        if (it.moon_interpretation) {
          dayCards.push('<article class="panchang-forecast-card"><h3>' + it.zodiac_ne + '</h3><p>' + it.moon_interpretation + '</p></article>');
        }
        if (it.remedy_tips) {
          var note = it.infeasible_transit_moon ? '<small>' + it.infeasible_transit_moon + '</small>' : '';
          nightCards.push('<article class="panchang-forecast-card"><h3>' + it.zodiac_ne + '</h3><p>' + it.remedy_tips + '</p>' + note + '</article>');
        }
        horoCards.push('<article class="horoscope-card"><h3>' + it.zodiac_ne + '</h3><p>' + (it.moon_interpretation || '') + '</p></article>');
      });
    }

    dayGrid.innerHTML = dayCards.length > 0 ? dayCards.join('') : '<p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>';
    nightGrid.innerHTML = nightCards.length > 0 ? nightCards.join('') : '<p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>';
    horoGrid.innerHTML = horoCards.length > 0 ? horoCards.join('') : '';
  }

  function updateHeroHeader(panchang) {
    var bs = panchang && panchang.bs_date;
    var calStrong = document.querySelector('.panchang-calendar strong');
    var calB = document.querySelector('.panchang-calendar b');
    if (bs) {
      if (calStrong) calStrong.textContent = toN(bs.d);
      if (calB) calB.textContent = formatBsDate(bs);
    } else {
      if (calStrong) calStrong.textContent = '-';
      if (calB) calB.textContent = '-';
    }
    var titleEl = document.querySelector('.panchang-hero h1');
    if (titleEl) {
      titleEl.textContent = (panchang && panchang.special_events_ne) ? panchang.special_events_ne : 'आजको पञ्चाङ्ग';
    }
  }

  function fadeIn(el) {
    if (!el) return;
    el.style.opacity = '0';
    el.style.transition = 'opacity .3s ease';
    requestAnimationFrame(function () {
      el.style.opacity = '1';
    });
  }

  function updateAll(data) {
    var pc = data && data.panchang ? data.panchang : null;
    var items = data && data.horoscope && data.horoscope.items ? data.horoscope.items : [];
    updatePanchang(pc);
    updatePanchangFacts(pc);
    updateSpecialEvents(pc);
    updateForecasts(items);
    updateHeroHeader(pc);
    fadeIn(document.querySelector('.panchang-detail-card'));
  }

  function fetchDataFromAd(adDateStr) {
    return Promise.all([
      fetch('/backend/api/panchang.php?date=' + encodeURIComponent(adDateStr)).then(function (r) { return r.json(); }).then(function (d) { return d.success ? d.data : null; }).catch(function () { return null; }),
      fetch('/backend/api/horoscope.php?date=' + encodeURIComponent(adDateStr)).then(function (r) { return r.json(); }).then(function (d) { return d.success ? d.data : null; }).catch(function () { return null; }),
    ]).then(function (results) {
      var data = { panchang: results[0] ? results[0].panchang : null, horoscope: results[1] ? results[1] : { items: [] } };
      var he = document.querySelector('.panchang-hero h1');
      if (he) he.textContent = 'लोड हुँदैछ…';
      if (data.panchang && data.panchang.bs_date) {
        setSelectsFromBs(data.panchang.bs_date);
      }
      updateAll(data);
      return data;
    });
  }

  function getAdDateStr() {
    var bs = getBsFromSelects();
    var ad = bs2ad(bs.y, bs.m, bs.d);
    var y = ad.getFullYear();
    var m = String(ad.getMonth() + 1).padStart(2, '0');
    var d = String(ad.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
  }

  function fetchData() {
    var adDateStr = getAdDateStr();
    return fetchDataFromAd(adDateStr);
  }

  function changeDate(daysDelta) {
    var bs = getBsFromSelects();
    var ad = bs2ad(bs.y, bs.m, bs.d);
    ad.setDate(ad.getDate() + daysDelta);
    var newBs = ad2bs(ad);
    setSelectsFromBs(newBs);
    fetchData();
  }

  function init() {
    var bsInit = initialData.bs_initial;
    if (bsInit) {
      yearSel.value = bsInit.y;
      monthSel.value = bsInit.m;
      updateMaxDay();
      daySel.value = Math.min(bsInit.d, parseInt(daySel.options[daySel.options.length - 1].value, 10));
    }

    yearSel.addEventListener('change', function () {
      updateMaxDay();
      fetchData();
    });
    monthSel.addEventListener('change', function () {
      updateMaxDay();
      fetchData();
    });
    daySel.addEventListener('change', fetchData);

    if (prevBtn) {
      prevBtn.addEventListener('click', function () { changeDate(-1); });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function () { changeDate(1); });
    }

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        tabBtns.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var tab = btn.getAttribute('data-tab');
        var contents = [tabPanchang, tabDay, tabNight];
        contents.forEach(function (el) { if (el) { el.style.opacity = '0'; } });
        setTimeout(function () {
          if (tabPanchang) tabPanchang.style.display = tab === 'पञ्चाङ्ग' ? 'block' : 'none';
          if (tabDay) tabDay.style.display = tab === 'दिन फल' ? 'block' : 'none';
          if (tabNight) tabNight.style.display = tab === 'रात्री फल' ? 'block' : 'none';
          contents.forEach(function (el) { if (el && el.style.display !== 'none') { el.style.opacity = '1'; } });
        }, 150);
        if (detailTitle) detailTitle.textContent = tab;
      });
    });

    updateAll(initialData);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
