(function () {
  'use strict';

  var SVG_NS = "http://www.w3.org/2000/svg";

  var NE_DIGITS = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
  function ne(n) { return String(n).split('').map(function (c) { return NE_DIGITS[+c] || c; }).join(''); }

  var NK_NAMES = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आद्रा', 'पुनर्वसु', 'पुष्य', 'आश्लेषा', 'मघा', 'पू.फाल्गुनी', 'उ.फाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पू.षाढा', 'उ.षाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पू.भाद्रपदा', 'उ.भाद्रपदा', 'रेवती'];
  var NK_LORDS = ['केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध', 'केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध', 'केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध'];
  var NK_STEP = 360 / 27;

  var RASHI_NAMES = ['मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
  var GRAHA_SHORT = { su: 'सू', mo: 'चं', ma: 'मं', me: 'बु', ju: 'गु', ve: 'शु', sa: 'श', ra: 'रा', ke: 'के' };

  /* Fixed South-Indian chart house positions (540x540) */
  var HOUSE_POS = {
    1:  { x: 455, y: 43 },
    2:  { x: 270, y: 171 },
    3:  { x: 92,  y: 44 },
    4:  { x: 42,  y: 92 },
    5:  { x: 187, y: 270 },
    6:  { x: 42,  y: 453 },
    7:  { x: 96,  y: 509 },
    8:  { x: 270, y: 341 },
    9:  { x: 458, y: 513 },
    10: { x: 500, y: 458 },
    11: { x: 391, y: 270 },
    12: { x: 504, y: 93 }
  };

  var RASHI_ORDER = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

  var form = document.getElementById('kundali-form');
  var resultDiv = document.getElementById('kundali-result');

  var toast = (function () {
    var el = document.getElementById('kundali-toast');
    if (!el) { el = document.createElement('div'); el.id = 'kundali-toast'; el.className = 'kundali-toast'; document.body.appendChild(el); }
    return el;
  })();

  function showToast(msg, type) {
    toast.textContent = msg;
    toast.className = 'kundali-toast ' + (type || '');
    toast.classList.add('show');
    if (toast._hideTimer) clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(function () { toast.classList.remove('show'); }, type === 'error' ? 5000 : 3000);
  }
  toast.addEventListener('click', function () {
    toast.classList.remove('show');
    if (toast._hideTimer) clearTimeout(toast._hideTimer);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    toast.classList.remove('show');
    resultDiv.style.display = 'none';

    var data = {};
    var fd = new FormData(form);
    fd.forEach(function (v, k) { data[k] = v; });

    var missing = [];
    if (!data.name || !data.name.trim()) missing.push('नाम');
    if (!data.phone || !data.phone.trim()) missing.push('फोन');
    if (!data.birth_year || data.birth_year === '') missing.push('जन्म वर्ष');
    if (!data.birth_month || data.birth_month === '') missing.push('जन्म महिना');
    if (!data.birth_day || data.birth_day === '') missing.push('जन्म गते');
    if (!data.birth_time || !data.birth_time.trim()) missing.push('जन्म समय');
    if (!data.birth_place || !data.birth_place.trim()) missing.push('जन्म स्थान');
    if (missing.length > 0) { showToast('कृपया ' + missing.join(', ') + ' भर्नुहोस्।', 'error'); return; }

    data.birth_date = [data.birth_year, data.birth_month, data.birth_day].join('-');

    fetch('/backend/api/kundali.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(function (res) { return res.json().then(function (json) { json._status = res.status; return json; }); })
    .then(function (json) {
      if (!json.success) throw new Error(json.message || 'गणना गर्न सकिएन।');
      renderKundali(json.data.kundali);
      resultDiv.style.display = 'block';
      resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
      showToast(json.data.message || 'कुण्डली तयार छ।', 'success');
    })
    .catch(function (err) { showToast(err.message || 'कृपया पुनः प्रयास गर्नुहोस्।', 'error'); });
  });

  /* ─── Utilities ─── */

  function degToDms(deg) {
    var d = Math.floor(deg);
    var m = Math.floor((deg - d) * 60);
    var s = Math.round(((deg - d) * 60 - m) * 60);
    return d + '\u00B0 ' + m + '\'' + (s > 0 ? ' ' + s + '"' : '');
  }

  function getNakshatra(deg) {
    var idx = Math.floor(deg / NK_STEP) % 27;
    return { name: NK_NAMES[idx], lord: NK_LORDS[idx], pada: Math.floor((deg % NK_STEP) / (NK_STEP / 4)) + 1 };
  }

  function sunDeclination(sunDeg) {
    var eps = 23.4393;
    var rad = Math.PI / 180;
    var dec = Math.asin(Math.sin(sunDeg * rad) * Math.sin(eps * rad)) / rad;
    var dir = dec >= 0 ? 'उ' : 'द';
    dec = Math.abs(dec);
    var d = Math.floor(dec);
    var m = Math.floor((dec - d) * 60);
    return dir + ',' + d + '\u00B0' + m + "'";
  }

  /* ─── Planet Table ─── */

  var P_ORDER = ['la', 'su', 'mo', 'ma', 'me', 'ju', 've', 'sa', 'ra', 'ke'];

  function renderTable(k) {
    var tbody = document.querySelector('#graha-table tbody');
    var html = '';
    for (var i = 0; i < P_ORDER.length; i++) {
      var key = P_ORDER[i];
      var p = k.planets[key];
      if (!p) continue;
      var nk = getNakshatra(p.deg);
      var retroCls = p.is_retro ? 'retrograde' : '';
      html += '<tr>' +
        '<td class="planet-name">' + p.name + '</td>' +
        '<td>' + ne(p.rashi_idx + 1) + ' (' + p.rashi + ')</td>' +
        '<td>' + ne(p.house) + ' / ' + degToDms(p.deg_in_rashi) + '</td>' +
        '<td>' + nk.name + '</td>' +
        '<td>' + nk.lord + '</td>' +
        '<td class="' + retroCls + '">' + (p.is_retro ? 'बक्री' : '') + '</td>' +
        '</tr>';
    }
    tbody.innerHTML = html;
  }

  /* ─── Report Meta ─── */

  function renderMeta(k) {
    var sun = k.planets.su;
    if (sun) {
      document.getElementById('meta-surya-kranti').textContent = sunDeclination(sun.deg);
    }
    document.getElementById('meta-ishtakal').textContent = k.ishtakal_ghati != null ? k.ishtakal_ghati.toFixed(2) + ' घटी' : '—';
  }

  /* ─── SVG Chart ─── */

  function createEl(name, attrs) {
    var el = document.createElementNS(SVG_NS, name);
    for (var k in attrs) { if (attrs.hasOwnProperty(k)) el.setAttribute(k, String(attrs[k])); }
    return el;
  }

  function drawBaseChart(svg) {
    svg.appendChild(createEl('rect', { x: 1.5, y: 1.5, width: 537, height: 537, rx: 1, class: 'chart-border' }));
    [
      "270,2 538,270 270,538 2,270 270,2",
      "2,2 270,270 538,2",
      "2,538 270,270 538,538"
    ].forEach(function (pts) { svg.appendChild(createEl('polyline', { points: pts, class: 'chart-main-line' })); });
    [
      [2, 2, 538, 538],
      [538, 2, 2, 538]
    ].forEach(function (l) { svg.appendChild(createEl('line', { x1: l[0], y1: l[1], x2: l[2], y2: l[3], class: 'chart-soft-line' })); });
  }

  function getHouseRashi(lagnaIdx, houseNum) {
    var taken = lagnaIdx + houseNum - 1;
    if (taken >= 12) taken -= 12;
    return taken;
  }

  function placeLabels(svg, k, isLagna) {
    var lagIdx = k.lagna_idx;
    var planets = k.planets;
    var usedHouses = {};
    var housePlanets = {};
    var houseIndexMap = {};

    for (var hi = 1; hi <= 12; hi++) {
      housePlanets[hi] = [];
      houseIndexMap[hi] = getHouseRashi(lagIdx, hi);
    }

    for (var pk in planets) {
      if (!planets.hasOwnProperty(pk) || pk === 'la') continue;
      var p = planets[pk];
      var h = p.house;
      if (housePlanets[h]) {
        housePlanets[h].push(GRAHA_SHORT[pk] || p.name);
      }
    }

    for (var hi = 1; hi <= 12; hi++) {
      var pos = HOUSE_POS[hi];
      var rIdx = houseIndexMap[hi];
      var rn = RASHI_NAMES[rIdx];

      /* House number */
      var hText = createEl('text', { x: pos.x, y: pos.y, class: 'house-no' });
      hText.textContent = ne(hi);
      svg.appendChild(hText);

      /* Rashi name below house number */
      var rText = createEl('text', { x: pos.x, y: pos.y + 34, class: 'planet-label', style: 'font-size:17px' });
      rText.textContent = rn;
      svg.appendChild(rText);

      /* Planet labels */
      var pts = housePlanets[hi];
      if (pts.length > 0) {
        var pText = createEl('text', { x: pos.x, y: pos.y + 62, class: 'center-label', style: 'font-size:16px' });
        pText.textContent = pts.join(' ');
        svg.appendChild(pText);
      }
    }

    /* Center lagna marker for lagna chart */
    if (isLagna) {
      var cText = createEl('text', { x: 270, y: 122, class: 'center-label' });
      cText.textContent = 'ल';
      svg.appendChild(cText);
    }
  }

  function addMarkerDef(svg) {
    var defs = createEl('defs');
    var marker = createEl('marker', {
      id: 'arrowHead', viewBox: '0 0 10 10', refX: 8.5, refY: 5,
      markerWidth: 7, markerHeight: 7, orient: 'auto-start-reverse'
    });
    marker.appendChild(createEl('path', { d: 'M 0 0 L 10 5 L 0 10 z', fill: '#324e5a' }));
    defs.appendChild(marker);
    svg.appendChild(defs);
  }

  function addNode(svg, opts) {
    svg.appendChild(createEl('circle', {
      cx: opts.x, cy: opts.y, r: opts.radius || 30,
      class: opts.solid ? 'node-solid' : 'node-circle'
    }));
    if (opts.label) {
      var t = createEl('text', { x: opts.x, y: opts.y + 1, class: 'node-label' });
      t.textContent = opts.label;
      svg.appendChild(t);
    }
  }

  function addArrow(svg, x1, y1, x2, y2) {
    svg.appendChild(createEl('line', { x1: x1, y1: y1, x2: x2, y2: y2, class: 'arrow-line', 'marker-end': 'url(#arrowHead)' }));
  }

  function drawLagnaOverlay(svg) {
    addMarkerDef(svg);
    addNode(svg, { x: 447, y: 268, label: 'सू रा', solid: true, radius: 29 });
    addNode(svg, { x: 447, y: 78, label: 'के', radius: 29 });
    addNode(svg, { x: 137, y: 268, radius: 27 });
    addNode(svg, { x: 270, y: 405, label: 'गु', radius: 27 });
    addArrow(svg, 447, 238, 447, 111);
    addArrow(svg, 416, 268, 167, 268);
    addArrow(svg, 426, 290, 291, 383);
  }

  function renderChart(hostId, k, isLagna) {
    var host = document.getElementById(hostId);
    host.innerHTML = '';
    var svg = createEl('svg', {
      viewBox: '0 0 540 540', role: 'img', class: 'kundli-svg',
      'aria-label': host.getAttribute('aria-label')
    });
    drawBaseChart(svg);
    placeLabels(svg, k, isLagna);
    if (isLagna) drawLagnaOverlay(svg);
    host.appendChild(svg);
    host.addEventListener('click', function () { host.classList.toggle('is-zoomed'); });
  }

  /* ─── Summary Header ─── */

  function renderSummary(k) {
    document.getElementById('kundali-heading').textContent = k.place + ' — ' + k.date;
  }

  /* ─── Main Render ─── */

  function renderKundali(k) {
    renderSummary(k);
    renderTable(k);
    renderMeta(k);
    renderChart('lagnaChart', k, true);
    renderChart('rashiChart', k, false);
  }

  /* Expose for debugging */
  window.renderKundali = renderKundali;

})();
