document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('contact-form');
  if (!form) return;
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    var data = Object.fromEntries(new FormData(form));
    var btn = document.getElementById('contact-submit');
    var msg = document.getElementById('contact-message');
    if (btn) btn.disabled = true;
    if (msg) msg.innerHTML = '';
    try {
      var res = await fetch('/backend/api/contact.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      var json = await res.json();
      if (json.success) {
        if (msg) msg.innerHTML = '<p class="success">' + (json.message || 'सन्देश सफलतापूर्वक पठाइयो।') + '</p>';
        form.reset();
      } else {
        if (msg) msg.innerHTML = '<p class="form-error">' + (json.message || 'सन्देश पठाउन सकिएन।') + '</p>';
      }
    } catch (err) {
      if (msg) msg.innerHTML = '<p class="form-error">सन्देश पठाउन सकिएन। कृपया फेरि प्रयास गर्नुहोस्।</p>';
    }
    if (btn) btn.disabled = false;
  });
});