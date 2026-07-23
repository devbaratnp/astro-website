var bookingForm = document.getElementById('booking-form');
var formServiceId = document.getElementById('form-service-id');
var formServiceTitle = document.getElementById('form-service-title');
var preferredDate = document.getElementById('preferred-date');
var submitBtn = document.getElementById('submit-btn');
var successMsg = document.getElementById('success-msg');
var errorMsg = document.getElementById('error-msg');

preferredDate.min = new Date().toISOString().slice(0, 10);

document.querySelectorAll('.book-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        formServiceId.value = btn.getAttribute('data-id');
        formServiceTitle.textContent = btn.getAttribute('data-title');
        bookingForm.style.display = '';
        successMsg.style.display = 'none';
        errorMsg.style.display = 'none';
        bookingForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

document.querySelectorAll('.download-materials').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var title = btn.getAttribute('data-title');
        var id = btn.getAttribute('data-id');
        var text = title + '\n\nसामान्य सामग्री: अक्षता, फूल, धूप, दीप, फलफूल, कलश, शुद्ध जल।\nविशेष सामग्रीका लागि कार्यालयले सम्पर्क गर्नेछ।';
        var blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'pooja-materials-' + id + '.txt';
        a.click();
        URL.revokeObjectURL(a.href);
    });
});

if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'पठाउँदै...';
        successMsg.style.display = 'none';
        errorMsg.style.display = 'none';

        var formData = new FormData(bookingForm);
        var payload = {
            service_id: formData.get('service_id'),
            name: formData.get('name'),
            phone: formData.get('phone'),
            email: formData.get('email') || '',
            preferred_date: formData.get('preferred_date'),
            preferred_time: formData.get('preferred_time') || '',
            address: formData.get('address') || '',
            needs_materials: formData.get('needs_materials') ? 1 : 0,
            is_live_stream: formData.get('is_live_stream') ? 1 : 0,
            special_instructions: formData.get('special_instructions') || ''
        };

        fetch('/backend/api/pooja.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (!res.success) {
                throw new Error(res.message || 'बुकिङ असफल भयो। कृपया फेरि प्रयास गर्नुहोस्।');
            }
            var bookingId = res.data ? res.data.id : '';
            successMsg.textContent = 'बुकिङ नं. ' + bookingId + ' सफल भयो। हामी चाँडै सम्पर्क गर्नेछौं।';
            successMsg.style.display = '';
            bookingForm.reset();
            submitBtn.disabled = false;
            submitBtn.textContent = 'पूजा बुक गर्नुहोस्';
        })
        .catch(function (err) {
            errorMsg.textContent = err.message || 'बुकिङ असफल भयो। कृपया फेरि प्रयास गर्नुहोस्।';
            errorMsg.style.display = '';
            submitBtn.disabled = false;
            submitBtn.textContent = 'पूजा बुक गर्नुहोस्';
        });
    });
}
