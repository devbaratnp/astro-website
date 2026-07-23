document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('kundali-form');
  var resultDiv = document.getElementById('kundali-result');
  var errorP = document.getElementById('kundali-error');

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    errorP.style.display = 'none';
    resultDiv.style.display = 'none';

    var data = {};
    var fd = new FormData(form);
    fd.forEach(function (v, k) { data[k] = v; });

    fetch('/backend/api/kundali.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      if (!json.success) throw new Error(json.message);
      var k = json.data.kundali;
      renderKundali(k);
      resultDiv.style.display = 'block';
      resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    })
    .catch(function (err) {
      errorP.textContent = err.message || 'कृपया पुनः प्रयास गर्नुहोस्।';
      errorP.style.display = 'block';
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

    // Planet table
    var tbody = document.querySelector('#graha-table tbody');
    var order = ['la', 'su', 'mo', 'ma', 'me', 'ju', 've', 'sa', 'ra', 'ke'];
    var html = '';
    for (var i = 0; i < order.length; i++) {
      var p = k.planets[order[i]];
      if (!p) continue;
      html += '<tr><td class="graha-sym">' + p.name + '</td><td>' + p.rashi + '</td><td>' + degToDms(p.deg_in_rashi) + '</td><td>' + p.house + '</td></tr>';
    }
    tbody.innerHTML = html;

    // Bhava grid
    var bhg = document.getElementById('bhava-grid');
    var bhHtml = '';
    for (var bh = 1; bh <= 12; bh++) {
      var h = k.houses[bh];
      bhHtml += '<div class="bhava-item"><span class="bh-num">भाव ' + bh + '</span><span class="bh-rashi">' + h.rashi + '</span><span class="bh-lord">' + h.lord + '</span></div>';
    }
    bhg.innerHTML = bhHtml;

    // Chart
    drawChart(k);
  }

  function drawChart(k) {
    var el = document.getElementById('kundali-chart');
    var size = 380;
    var cx = size / 2, cy = size / 2;
    var outerR = size / 2 - 10;
    var cellR = outerR / 4.5;
    var svg = '<svg class="chart-svg" viewBox="0 0 ' + size + ' ' + size + '" xmlns="http://www.w3.org/2000/svg">';

    // Standard North Indian chart layout (4x4 grid)
    // House positions in the grid (row, col):
    // [12]  [1]   [2]   [3]
    // [11]  [--]  [--]  [4]
    // [10]  [--]  [--]  [5]
    // [9]   [8]   [7]   [6]
    var positions = [
      { r: 0, c: 1 }, // House 1 (lagna)  — top row, 2nd col
      { r: 0, c: 2 }, // House 2
      { r: 0, c: 3 }, // House 3
      { r: 1, c: 3 }, // House 4
      { r: 2, c: 3 }, // House 5
      { r: 3, c: 3 }, // House 6
      { r: 3, c: 2 }, // House 7
      { r: 3, c: 1 }, // House 8
      { r: 3, c: 0 }, // House 9
      { r: 2, c: 0 }, // House 10
      { r: 1, c: 0 }, // House 11
      { r: 0, c: 0 }, // House 12
    ];

    var margin = 2;
    var cellW = (size - 2 * margin) / 4;
    var cellH = (size - 2 * margin) / 4;
    var lagRashi = k.lagna_idx;
    var rashiNames = ['\u092E\u0947\u0937', '\u0935\u0943\u0937', '\u092E\u093F\u0925\u0941\u0928', '\u0915\u0930\u094D\u0915\u091F', '\u0938\u093F\u0902\u0939', '\u0915\u0928\u094D\u092F\u093E', '\u0924\u0941\u0932\u093E', '\u0935\u0943\u0936\u094D\u091A\u093F\u0915', '\u0927\u0928\u0941', '\u092E\u0915\u0930', '\u0915\u0941\u092E\u094D\u092D', '\u092E\u0940\u0928'];

    var pOrder = ['su', 'mo', 'ma', 'me', 'ju', 've', 'sa', 'ra', 'ke'];
    var pShort = { su: '\u0938\u0942', mo: '\u091A\u0928\u094D\u0926\u094D\u0930', ma: '\u092E\u0902', me: '\u092C\u0941', ju: '\u0917\u0941', ve: '\u0936\u0941', sa: '\u0936\u0928\u093F', ra: '\u0930\u093E', ke: '\u0915\u0947' };

    for (var hi = 0; hi < 12; hi++) {
      var pos = positions[hi];
      var x = margin + pos.c * cellW;
      var y = margin + pos.r * cellH;
      var cx = x + cellW / 2;
      var cy = y + cellH / 2;
      var ri = (lagRashi + hi) % 12;
      var rn = rashiNames[ri];

      // Cell rect
      svg += '<rect class="chart-cell" x="' + x + '" y="' + y + '" width="' + cellW + '" height="' + cellH + '" rx="3"/>';

      // Rashi number (top-left corner)
      svg += '<text class="chart-num" x="' + (x + 6) + '" y="' + (y + 14) + '">' + (ri + 1) + '</text>';

      // Lagna marker for house 1
      if (hi === 0) {
        svg += '<text class="chart-lagna" x="' + (cx) + '" y="' + (y + 14) + '">\u0932\u0917\u094D\u0928</text>';
      }

      // Rashi name
      svg += '<text class="chart-label" x="' + cx + '" y="' + (cy - 4) + '">' + rn + '</text>';

      // Planets in this rashi
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