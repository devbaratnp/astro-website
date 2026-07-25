(function() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    var CSRF_TOKEN = meta ? meta.content : '';
    var body = document.body;
    var API_BASE = (body ? body.getAttribute('data-api-base') : '') || '/backend/api';

    function getCsrf() { return CSRF_TOKEN; }

    function clearZone(field) {
        var wrap = document.querySelector('.file-upload-wrap[data-field="' + field + '"]');
        if (!wrap) return;
        wrap.querySelector('.file-zone').innerHTML = '<span><span class="upload-icon">\u2601\uFE0F</span>Drop image here or click to upload</span>';
        wrap.querySelector('.file-hidden').value = '';
        var btn = wrap.querySelector('.file-clear');
        if (btn) btn.remove();
    }

    function previewImage(url, field) {
        var zone = document.querySelector('.file-zone[data-field="' + field + '"]');
        if (!zone) return;
        zone.innerHTML = '<img src="' + url + '" alt="preview" class="file-preview">';
        var wrap = document.querySelector('.file-upload-wrap[data-field="' + field + '"]');
        if (wrap) {
            wrap.querySelector('.file-hidden').value = url;
            if (!wrap.querySelector('.file-clear')) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'file-clear';
                btn.dataset.field = field;
                btn.setAttribute('aria-label', 'Clear image');
                btn.textContent = '\u2715';
                wrap.appendChild(btn);
            }
        }
    }

    function createFileInput(zone) {
        var input = zone.querySelector('input[type="file"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp,image/gif,image/svg+xml';
            input.style.display = 'none';
            zone.appendChild(input);
        }
        return input;
    }

    function uploadFile(file, field, uploadType) {
        var zone = document.querySelector('.file-zone[data-field="' + field + '"]');
        if (!zone) return;
        zone.innerHTML = '<span><span class="upload-icon">\u23F3</span>Uploading...</span>';

        var fd = new FormData();
        fd.append('file', file);
        fd.append('type', uploadType);
        fd.append('_csrf', getCsrf());

        fetch(API_BASE + '/upload.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.success) throw new Error(d.message || 'Upload failed');
            previewImage(d.data.url, field);
        })
        .catch(function() {
            zone.innerHTML = '<span><span class="upload-icon">\u26A0\uFE0F</span>Upload failed. Try again.</span>';
        });
    }

    function isProductZone(zone) {
        return zone && zone.getAttribute('data-upload-type') === 'product';
    }

    document.addEventListener('click', function(e) {
        var clearBtn = e.target.closest('.file-clear');
        if (clearBtn) {
            clearZone(clearBtn.dataset.field);
        }
    });

    document.addEventListener('click', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone || isProductZone(zone)) return;
        var input = createFileInput(zone);
        input.click();
    });

    document.addEventListener('change', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone || isProductZone(zone)) return;
        var fileInput = zone.querySelector('input[type="file"]');
        if (fileInput && e.target === fileInput && fileInput.files[0]) {
            var uploadType = zone.getAttribute('data-upload-type') || 'general';
            uploadFile(fileInput.files[0], zone.dataset.field, uploadType);
            fileInput.value = '';
        }
    });

    document.addEventListener('dragover', function(e) {
        var zone = e.target.closest('.file-zone');
        if (zone && !isProductZone(zone)) { e.preventDefault(); zone.classList.add('drag-over'); }
    });

    document.addEventListener('dragleave', function(e) {
        var zone = e.target.closest('.file-zone');
        if (zone && !isProductZone(zone)) zone.classList.remove('drag-over');
    });

    document.addEventListener('drop', function(e) {
        var zone = e.target.closest('.file-zone');
        if (!zone || isProductZone(zone)) return;
        e.preventDefault();
        zone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        if (file) {
            var uploadType = zone.getAttribute('data-upload-type') || 'general';
            uploadFile(file, zone.dataset.field, uploadType);
        }
    });
})();
