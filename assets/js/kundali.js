document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('kundali-form');
  var resultDiv = document.getElementById('kundali-result');

  var toast = document.getElementById('kundali-toast') || (function () {
    var el = document.createElement('div');
    el.id = 'kundali-toast';
    el.className = 'kundali-toast';
    document.body.appendChild(el);
    return el;
  })();

  function showToast(msg, type) {
    toast.textContent = msg;
    toast.className = 'kundali-toast ' + (type || '');
    toast.classList.add('show');
    if (toast._hideTimer) clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(function () {
      toast.classList.remove('show');
    }, type === 'error' ? 5000 : 3000);
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

    if (missing.length > 0) {
      showToast('कृपया ' + missing.join(', ') + ' भर्नुहोस्।', 'error');
      return;
    }

    data.birth_date = [data.birth_year, data.birth_month, data.birth_day].join('-');

    fetch('/backend/api/kundali.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(function (res) {
      return res.json().then(function (json) {
        json._status = res.status;
        return json;
      });
    })
    .then(function (json) {
      if (!json.success) throw new Error(json.message || 'गणना गर्न सकिएन।');
      var k = json.data.kundali;
      renderKundali(k);
      resultDiv.style.display = 'block';
      resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
      showToast(json.data.message || 'कुण्डली तयार छ।', 'success');
    })
    .catch(function (err) {
      showToast(err.message || 'कृपया पुनः प्रयास गर्नुहोस्।', 'error');
    });
  });

  function degToDms(deg) {
    var d = Math.floor(deg);
    var m = Math.floor((deg - d) * 60);
    var s = Math.round(((deg - d) * 60 - m) * 60);
    return d + '\u00B0 ' + m + '\'' + (s > 0 ? ' ' + s + '"' : '');
  }

  function renderKundali(k) {
    document.getElementById('kundali-heading').textContent = k.place + ' — ' + k.date;
    document.getElementById('kr-rashi').textContent = k.rashi;
    document.getElementById('kr-nakshatra').textContent = k.nakshatra + ' (' + k.nakshatra_lord + ')';
    document.getElementById('kr-lagna').textContent = k.lagna;
    document.getElementById('kr-navamsha').textContent = k.navamsha_rashi;
    document.getElementById('kr-pada').textContent = k.nakshatra_pada;
    var mel = document.getElementById('kr-mangal');
    if (k.mangal_dosha) {
      mel.textContent = 'दोष छ (भाव ' + k.mangal_house + ')';
      mel.className = 'yes';
    } else {
      mel.textContent = 'दोष छैन';
      mel.className = 'no';
    }

    var tbody = document.querySelector('#graha-table tbody');
    var order = ['la', 'su', 'mo', 'ma', 'me', 'ju', 've', 'sa', 'ra', 'ke'];
    var html = '';
    for (var i = 0; i < order.length; i++) {
      var p = k.planets[order[i]];
      if (!p) continue;
      html += '<tr><td class="graha-sym">' + p.name + '</td><td>' + p.rashi + '</td><td>' + degToDms(p.deg_in_rashi) + '</td><td>' + p.house + '</td></tr>';
    }
    tbody.innerHTML = html;

    var bhg = document.getElementById('bhava-grid');
    var bhHtml = '';
    for (var bh = 1; bh <= 12; bh++) {
      var h = k.houses[bh];
      bhHtml += '<div class="bhava-item"><span class="bh-num">भाव ' + bh + '</span><span class="bh-rashi">' + h.rashi + '</span><span class="bh-lord">' + h.lord + '</span></div>';
    }
    bhg.innerHTML = bhHtml;

    drawChart(k);
  }

  function drawChart(k) {
    var el = document.getElementById('kundali-chart');
    var size = 380;
    var margin = 2;
    var cellW = (size - 2 * margin) / 4;
    var cellH = (size - 2 * margin) / 4;
    var lagRashi = k.lagna_idx;
    var rashiNames = ['\u092E\u0947\u0937', '\u0935\u0943\u0937', '\u092E\u093F\u0925\u0941\u0928', '\u0915\u0930\u094D\u0915\u091F', '\u0938\u093F\u0902\u0939', '\u0915\u0928\u094D\u092F\u093E', '\u0924\u0941\u0932\u093E', '\u0935\u0943\u0936\u094D\u091A\u093F\u0915', '\u0927\u0928\u0941', '\u092E\u0915\u0930', '\u0915\u0941\u092E\u094D\u092D', '\u092E\u0940\u0928'];

    var pOrder = ['su', 'mo', 'ma', 'me', 'ju', 've', 'sa', 'ra', 'ke'];
    var pShort = { su: '\u0938\u0942', mo: '\u091A\u0928\u094D\u0926\u094D\u0930', ma: '\u092E\u0902', me: '\u092C\u0941', ju: '\u0917\u0941', ve: '\u0936\u0941', sa: '\u0936\u0928\u093F', ra: '\u0930\u093E', ke: '\u0915\u0947' };

    var positions = [
      { r: 0, c: 1 }, { r: 0, c: 2 }, { r: 0, c: 3 },
      { r: 1, c: 3 }, { r: 2, c: 3 }, { r: 3, c: 3 },
      { r: 3, c: 2 }, { r: 3, c: 1 }, { r: 3, c: 0 },
      { r: 2, c: 0 }, { r: 1, c: 0 }, { r: 0, c: 0 },
    ];

    var svg = '<svg class="chart-svg" viewBox="0 0 ' + size + ' ' + size + '" xmlns="http://www.w3.org/2000/svg">';

    for (var hi = 0; hi < 12; hi++) {
      var pos = positions[hi];
      var x = margin + pos.c * cellW;
      var y = margin + pos.r * cellH;
      var cx = x + cellW / 2;
      var cy = y + cellH / 2;
      var ri = (lagRashi + hi) % 12;
      var rn = rashiNames[ri];

      svg += '<rect class="chart-cell" x="' + x + '" y="' + y + '" width="' + cellW + '" height="' + cellH + '" rx="3"/>';
      svg += '<text class="chart-num" x="' + (x + 6) + '" y="' + (y + 14) + '">' + (ri + 1) + '</text>';

      if (hi === 0) {
        svg += '<text class="chart-lagna" x="' + cx + '" y="' + (y + 14) + '">\u0932\u0917\u094D\u0928</text>';
      }

      svg += '<text class="chart-label" x="' + cx + '" y="' + (cy - 4) + '">' + rn + '</text>';

      var pts = [];
      for (var pi = 0; pi < pOrder.length; pi++) {
        var pd = k.planets[pOrder[pi]];
        if (pd && pd.rashi_idx === ri) pts.push(pShort[pOrder[pi]]);
      }
      if (pts.length) {
        svg += '<text class="chart-planet" x="' + cx + '" y="' + (cy + 16) + '">' + pts.join(' ') + '</text>';
      }
    }

    svg += '</svg>';
    el.innerHTML = svg;
  }
});
