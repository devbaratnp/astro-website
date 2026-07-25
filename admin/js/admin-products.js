(function() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    var CSRF_TOKEN = meta ? meta.content : '';
    var body = document.body;
    var API_BASE = (body ? body.getAttribute('data-api-base') : '') || '/backend/api';

    var el = document.getElementById('product-images-data');
    if (!el) return;

    function getImageUrls() {
        try { return JSON.parse(el.value || '[]'); } catch(e) { return []; }
    }

    function setImageUrls(urls) {
        el.value = JSON.stringify(urls);
        renderPreview(urls);
    }

    function renderPreview(urls) {
        var container = document.getElementById('product-images-preview');
        if (!container) return;
        container.innerHTML = urls.map(function(url) {
            return '<div class="multi-preview-item">' +
                '<img src="' + url.replace(/'/g, "\\'") + '" alt="">' +
                '<button type="button" class="multi-preview-remove" data-url="' + url.replace(/'/g, "\\'") + '" aria-label="Remove image">\u2715</button>' +
            '</div>';
        }).join('');
    }

    document.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.multi-preview-remove');
        if (!removeBtn) return;
        var url = removeBtn.dataset.url;
        var urls = getImageUrls().filter(function(u) { return u !== url; });
        setImageUrls(urls);
    });

    document.addEventListener('click', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        var input = zone.querySelector('input[type="file"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp,image/gif';
            input.style.display = 'none';
            zone.appendChild(input);
        }
        input.click();
    });

    document.addEventListener('change', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        var fileInput = zone.querySelector('input[type="file"]');
        if (fileInput && e.target === fileInput && fileInput.files[0]) {
            uploadProductImage(fileInput.files[0]);
            fileInput.value = '';
        }
    });

    document.addEventListener('dragover', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (zone) { e.preventDefault(); zone.classList.add('drag-over'); }
    });

    document.addEventListener('dragleave', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (zone) zone.classList.remove('drag-over');
    });

    document.addEventListener('drop', function(e) {
        var zone = e.target.closest('.file-zone[data-field="product_images"]');
        if (!zone) return;
        e.preventDefault();
        zone.classList.remove('drag-over');
        var file = e.dataTransfer.files[0];
        if (file) uploadProductImage(file);
    });

    function uploadProductImage(file) {
        var zone = document.querySelector('.file-zone[data-field="product_images"]');
        if (!zone) return;
        zone.innerHTML = '<span><span class="upload-icon">\u23F3</span>Uploading...</span>';

        var fd = new FormData();
        fd.append('file', file);
        fd.append('type', 'general');
        fd.append('_csrf', CSRF_TOKEN);

        fetch(API_BASE + '/upload.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.success) throw new Error(d.message || 'Upload failed');
            var urls = getImageUrls();
            urls.push(d.data.url);
            setImageUrls(urls);
            zone.innerHTML = '<span><span class="upload-icon">\u2601\uFE0F</span>Tap to add product image</span>';
        })
        .catch(function() {
            zone.innerHTML = '<span><span class="upload-icon">\u26A0\uFE0F</span>Upload failed. Try again.</span>';
        });
    }

    renderPreview(getImageUrls());
})();
