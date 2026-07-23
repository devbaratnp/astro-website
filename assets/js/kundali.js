document.addEventListener('DOMContentLoaded', function () {
  var NEPALI_DIGITS = ['\u0966', '\u0967', '\u0968', '\u0969', '\u096A', '\u096B', '\u096C', '\u096D', '\u096E', '\u096F'];

  function toNepaliNum(n) {
    return String(n).split('').map(function (c) { return NEPALI_DIGITS[+c] || c; }).join('');
  }

  var form = document.getElementById('kundali-form');
  var resultDiv = document.getElementById('kundali-result');
  var errorP = document.getElementById('kundali-error');
  var chart = document.getElementById('lagna-chart');

  for (var i = 1; i <= 12; i++) {
    var span = document.createElement('span');
    span.textContent = toNepaliNum(i);
    chart.appendChild(span);
  }

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
      document.getElementById('result-rashi').textContent = k.rashi;
      document.getElementById('result-nakshatra').textContent = k.nakshatra;
      document.getElementById('result-lagna').textContent = k.lagna;
      document.getElementById('result-place').textContent = k.place;
      resultDiv.style.display = 'grid';
    })
    .catch(function (err) {
      errorP.textContent = err.message || '\u0915\u0943\u092A\u092F\u093E \u092A\u0941\u0928\u0903 \u092A\u094D\u0930\u092F\u093E\u0938 \u0917\u0930\u094D\u0928\u0941\u0939\u094B\u0938\u094D\u0964';
      errorP.style.display = 'block';
    });
  });
});
