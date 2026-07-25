(function() {
    function toggleDetails(id) {
        var row = document.getElementById('details-' + id);
        if (!row) return;
        row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
    }
    window.toggleDetails = toggleDetails;

    function toggleMsg(id) {
        var row = document.getElementById('msg-' + id);
        if (!row) return;
        row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
    }
    window.toggleMsg = toggleMsg;

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        var action = btn.getAttribute('data-action');
        var id = btn.getAttribute('data-id');
        if (action === 'toggle-msg' && id) toggleMsg(id);
        if (action === 'toggle-details' && id) toggleDetails(id);
    });

    document.addEventListener('click', function(e) {
        var confirmEl = e.target.closest('[data-confirm]');
        if (!confirmEl) return;
        e.preventDefault();
        var msg = confirmEl.getAttribute('data-confirm');
        if (confirm(msg)) {
            if (confirmEl.hasAttribute('href')) {
                window.location.href = confirmEl.getAttribute('href');
            }
        }
    });

    document.addEventListener('click', function(e) {
        var clearBtn = e.target.closest('.multi-preview-remove');
        if (!clearBtn) return;
        var url = clearBtn.dataset.url;
        var el = document.getElementById('product-images-data');
        if (!el) return;
        try {
            var urls = JSON.parse(el.value || '[]').filter(function(u) { return u !== url; });
            el.value = JSON.stringify(urls);
            var container = document.getElementById('product-images-preview');
            if (container) {
                container.innerHTML = urls.map(function(u) {
                    return '<div class="multi-preview-item"><img src="' + u.replace(/'/g, "\\'") + '" alt=""><button type="button" class="multi-preview-remove" data-url="' + u.replace(/'/g, "\\'") + '" aria-label="Remove image">\u2715</button></div>';
                }).join('');
            }
        } catch(e) {}
    });

    var pwToggle = document.getElementById('pwToggle');
    var pw = document.getElementById('loginPassword');
    if (pwToggle && pw) {
        pwToggle.addEventListener('click', function() {
            var show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            var e1 = document.getElementById('eyeSlash1');
            var e2 = document.getElementById('eyeSlash2');
            var e3 = document.getElementById('eyeIcon');
            var e4 = document.getElementById('eyePupil');
            if (e1) e1.style.display = show ? 'block' : 'none';
            if (e2) e2.style.display = show ? 'block' : 'none';
            if (e3) e3.style.display = show ? 'none' : 'block';
            if (e4) e4.style.display = show ? 'none' : 'block';
            pwToggle.setAttribute('aria-label', show ? '\u092A\u093E\u0938\u0935\u0930\u094D\u0921 \u0932\u0941\u0915\u093E\u0909\u0928\u0941\u0939\u094B\u0938\u094D' : '\u092A\u093E\u0938\u0935\u0930\u094D\u0921 \u0926\u0947\u0916\u093E\u0928\u0941\u0939\u094B\u0938\u094D');
        });
    }
})();
