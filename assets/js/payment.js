var form = document.getElementById('payment-form');
var submitBtn = document.getElementById('submit-btn');
var successMsg = document.getElementById('success-msg');
var errorMsg = document.getElementById('error-msg');

form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitBtn.disabled = true;
    submitBtn.textContent = 'पठाउँदै...';
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';

    var formData = new FormData(form);
    var file = formData.get('screenshot');
    if (!file || !file.size) {
        errorMsg.textContent = 'कृपया स्क्रिनसट संलग्न गर्नुहोस्।';
        errorMsg.style.display = '';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Verification का लागि पठाउनुहोस्';
        return;
    }

    var reader = new FileReader();
    reader.onload = function () {
        var base64 = reader.result.split(',')[1];
        var payload = {
            booking_type: formData.get('booking_type'),
            booking_id: formData.get('booking_id'),
            user_name: formData.get('user_name'),
            user_phone: formData.get('user_phone'),
            amount: formData.get('amount'),
            method: formData.get('method'),
            transaction_ref: formData.get('transaction_ref'),
            screenshot: base64
        };

        fetch('/backend/api/payments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Verification का लागि पठाउनुहोस्';
            if (!res.success) {
                throw new Error(res.message || 'पठाउन सकिएन। कृपया फेरि प्रयास गर्नुहोस्।');
            }
            successMsg.style.display = '';
            successMsg.textContent = 'भुक्तानी विवरण नं. ' + (res.data && res.data.id) + ' प्राप्त भयो। प्रशासकले पुष्टि गरेपछि सूचित गरिनेछ।';
            form.reset();
        })
        .catch(function (err) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Verification का लागि पठाउनुहोस्';
            errorMsg.textContent = err.message || 'पठाउन सकिएन। कृपया फेरि प्रयास गर्नुहोस्।';
            errorMsg.style.display = '';
        });
    };
    reader.onerror = function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Verification का लागि पठाउनुहोस्';
        errorMsg.textContent = 'स्क्रिनसट पढ्न सकिएन। कृपया फेरि प्रयास गर्नुहोस्।';
        errorMsg.style.display = '';
    };
    reader.readAsDataURL(file);
});
