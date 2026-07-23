(function () {
  var dataEl = document.getElementById('panchang-data');
  var initialData = dataEl ? JSON.parse(dataEl.textContent) : { panchang: null, horoscope: { items: [] } };

  var dateInput = document.getElementById('panchang-date');
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

  function toNepaliDateStr(dateVal) {
    try {
      return new Intl.DateTimeFormat('ne-NP', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(dateVal + 'T12:00:00'));
    } catch (e) {
      return dateVal;
    }
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

  function updateHeroHeader(dateVal, panchang) {
    var dt = new Date(dateVal + 'T12:00:00');
    var dayNum = dt.getDate();
    var dateStr = toNepaliDateStr(dateVal);
    var calStrong = document.querySelector('.panchang-calendar strong');
    var calB = document.querySelector('.panchang-calendar b');
    if (calStrong) calStrong.textContent = dayNum;
    if (calB) calB.textContent = dateStr;
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

  function updateAll(dateVal, data) {
    var pc = data && data.panchang ? data.panchang : null;
    var items = data && data.horoscope && data.horoscope.items ? data.horoscope.items : [];
    updatePanchang(pc);
    updatePanchangFacts(pc);
    updateSpecialEvents(pc);
    updateForecasts(items);
    updateHeroHeader(dateVal, pc);
    fadeIn(document.querySelector('.panchang-detail-card'));
  }

  function fetchData(dateVal) {
    return Promise.all([
      fetch('/backend/api/panchang.php?date=' + encodeURIComponent(dateVal)).then(function (r) { return r.json(); }).then(function (d) { return d.success ? d.data : null; }).catch(function () { return null; }),
      fetch('/backend/api/horoscope.php?date=' + encodeURIComponent(dateVal)).then(function (r) { return r.json(); }).then(function (d) { return d.success ? d.data : null; }).catch(function () { return null; }),
    ]).then(function (results) {
      var data = { panchang: results[0] ? results[0].panchang : null, horoscope: results[1] ? results[1] : { items: [] } };
      var he = document.querySelector('.panchang-hero h1');
      if (he) he.textContent = 'लोड हुँदैछ…';
      updateAll(dateVal, data);
      return data;
    });
  }

  function changeDate(daysDelta) {
    var dt = new Date(dateInput.value + 'T12:00:00');
    dt.setDate(dt.getDate() + daysDelta);
    var newDate = dt.toISOString().slice(0, 10);
    dateInput.value = newDate;
    fetchData(newDate);
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', function () { changeDate(-1); });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function () { changeDate(1); });
  }
  if (dateInput) {
    dateInput.addEventListener('change', function () {
      fetchData(dateInput.value);
    });
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

  updateAll(dateInput ? dateInput.value : new Date().toISOString().slice(0, 10), initialData);
})();