/* ═══════════════════════════════════════════
   श्रीहरि ज्योतिष — Admin Panel JS
   Vanilla JS — no dependencies
   ═══════════════════════════════════════════ */

/* ── Config ── */
const API_BASE = '/backend/api';

const SECTIONS = [
  ['dashboard', 'Dashboard', '📊'],
  ['appointments', 'Appointments', '📅'],
  ['pooja', 'Pooja orders', '🙏'],
  ['payments', 'Payments', '💳'],
  ['services', 'Pooja services', '📋'],
  ['articles', 'Articles', '📖'],
  ['events', 'Events & Tours', '📅'],
  ['gallery', 'Gallery', '🖼'],
  ['testimonials', 'Testimonials', '⭐'],
  ['panchang', 'Panchang', '📅'],
];

const EDITORS = {
  services: [
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['category', 'Category'],
    ['base_price', 'Price'],
    ['duration_minutes', 'Duration (min)'],
  ],
  articles: [
    ['title_ne', 'Nepali title'],
    ['slug', 'URL slug'],
    ['excerpt_ne', 'Excerpt'],
    ['content_ne', 'Content'],
  ],
  panchang: [
    ['date', 'Date'],
    ['tithi', 'Tithi'],
    ['nakshatra', 'Nakshatra'],
    ['sunrise', 'Sunrise'],
    ['sunset', 'Sunset'],
    ['special_events_ne', 'Special events'],
  ],
  testimonials: [
    ['name', 'Name'],
    ['title', 'Title'],
    ['content', 'Content'],
    ['rating', 'Rating (1–5)'],
    ['location', 'Location'],
    ['sort_order', 'Sort order'],
  ],
  events: [
    ['type', 'Type (event/tour)'],
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['date_from', 'Date from'],
    ['location', 'Location'],
    ['contact_person', 'Contact person'],
    ['contact_phone', 'Contact phone'],
  ],
  gallery: [
    ['type', 'Type (image/video/audio)'],
    ['title_ne', 'Nepali title'],
    ['url', 'URL'],
    ['thumbnail', 'Thumbnail URL'],
    ['embed_url', 'Embed URL'],
    ['source', 'Source'],
  ],
};

const IMAGE_FIELDS = {
  articles: ['cover_image'],
  events: ['cover_image'],
  gallery: ['url', 'thumbnail'],
  testimonials: ['photo'],
};

const STATUSES = {
  appointments: ['pending', 'confirmed', 'completed', 'cancelled'],
  pooja: ['pending', 'confirmed', 'completed', 'cancelled'],
  payments: ['pending', 'approved', 'rejected'],
};

const SECTION_SELECTS = {
  events: { type: ['event', 'Event', 'tour', 'Tour'] },
};

/* ── API helper ── */
async function api(path, options = {}) {
  const response = await fetch(`${API_BASE}/${path}`, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const body = await response.json().catch(() => ({
    success: false,
    message: `Server error (${response.status})`,
  }));
  if (!body.success) throw new Error(body.message || 'Request failed');
  return body.data;
}

/* ── Auth ── */
async function checkAuth() {
  try {
    return await api('auth.php');
  } catch {
    window.location.href = 'login.html';
    return null;
  }
}

function logout() {
  api('auth.php?logout=1').then(() => {
    window.location.href = 'login.html';
  });
}

/* ── Layout builders ── */
function buildSidebar(activeKey) {
  const navLinks = SECTIONS.map(([key, label, icon]) =>
    `<a href="${key === 'dashboard' ? 'dashboard.html' : 'manage.html?section=' + key}" class="${activeKey === key ? 'active' : ''}">
      <span class="nav-icon">${icon}</span>
      <span class="nav-label">${label}</span>
    </a>`
  ).join('');

  return `
    <aside>
      <div class="admin-brand">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
        <span>Shreehari Admin<small>Management system</small></span>
      </div>
      <nav>${navLinks}</nav>
      <div class="admin-user">
        <a href="#" id="logoutBtn">🚪 <span class="nav-label">Logout</span></a>
      </div>
    </aside>`;
}

function buildTopbar(title, sectionKey) {
  const current = SECTIONS.find(s => s[0] === sectionKey);
  const icon = current ? current[2] : '📊';
  return `
    <div class="admin-topbar">
      <div style="display:flex;align-items:center;gap:8px">
        <button class="sidebar-toggle mobile-toggle" id="mobileToggle" aria-label="Open sidebar">☰</button>
        <h2><span>${icon}</span>${title}</h2>
      </div>
      <a href="/" class="view-site">🏠 View site</a>
    </div>`;
}

/* ── Content builders ── */
function buildDashboard(data) {
  if (!data) return '<div class="admin-loading">Loading...</div>';
  const cards = Object.entries(data).map(([k, v]) =>
    `<article><strong>${v}</strong><span>${k.replace(/_/g, ' ')}</span></article>`
  ).join('');
  return `<div class="admin-stats">${cards}</div>`;
}

function buildTable(rows, section) {
  if (!rows || !rows.length) {
    return '<div class="admin-empty">No records yet.</div>';
  }

  const hidden = ['admin_notes', 'message', 'content_ne', 'content_en',
    'description_ne', 'description_en', 'password_hash'];

  const keys = Object.keys(rows[0])
    .filter(k => !hidden.includes(k))
    .slice(0, 6);

  const thead = keys.map(k => `<th>${k.replace(/_/g, ' ')}</th>`).join('');

  const tbody = rows.map((r, i) => {
    const cells = keys.map(k => {
      const val = r[k];
      if (typeof val === 'string' && val.startsWith('http')) {
        return `<td><img src="${val}" alt="" class="cell-thumb"></td>`;
      }
      return `<td>${val ?? '—'}</td>`;
    }).join('');

    let actions = '<td class="admin-actions">';

    if (STATUSES[section]) {
      actions += `<select class="status-select" data-id="${r.id}">
        ${STATUSES[section].map(s =>
          `<option value="${s}" ${r.status === s ? 'selected' : ''}>${s}</option>`
        ).join('')}
      </select>`;
    }

    if (EDITORS[section]) {
      actions += `<button class="edit-btn" data-id="${r.id}">Edit</button>
        <button class="danger delete-btn" data-id="${r.id}">Delete</button>`;
    }

    actions += '</td>';
    return `<tr>${cells}${actions}</tr>`;
  }).join('');

  return `
    <div class="admin-table-wrap">
      <table>
        <thead><tr>${thead}<th>Actions</th></tr></thead>
        <tbody>${tbody}</tbody>
      </table>
    </div>`;
}

function buildEditor(section, editing) {
  const fields = EDITORS[section];
  if (!fields) return '';

  const isEdit = editing && editing.id;
  const selects = SECTION_SELECTS[section] || {};

  const fieldHtml = fields.map(([name, label]) => {
    const isImage = IMAGE_FIELDS[section] && IMAGE_FIELDS[section].includes(name);
    const isSlug = name === 'slug';
    const isSelect = selects[name];

    let input = '';
    if (isImage) {
      const val = editing ? (editing[name] || '') : '';
      input = `<div class="file-upload-wrap" data-field="${name}">
        <div class="file-zone" data-field="${name}">
          ${val ? `<img src="${val}" alt="preview" class="file-preview">` :
            `<span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>`}
        </div>
        <input type="file" accept="image/*" style="display:none" class="file-input" data-field="${name}">
        ${val ? `<button type="button" class="file-clear" data-field="${name}">✕</button>` : ''}
        <input type="hidden" name="${name}" value="${val}" class="file-hidden">
      </div>
      <div class="upload-progress" style="display:none;font-size:11px;color:var(--muted);margin-top:4px"></div>`;
    } else if (isSlug) {
      input = `<input type="text" name="${name}" value="${editing ? (editing[name] || '') : ''}"
        placeholder="auto-generated" id="slugInput">`;
    } else if (isSelect) {
      const opts = isSelect;
      input = `<select name="${name}">
        <option value="">— Select —</option>`;
      for (let i = 0; i < opts.length; i += 2) {
        const val = opts[i];
        const lbl = opts[i + 1];
        const sel = editing && editing[name] === val ? 'selected' : '';
        input += `<option value="${val}" ${sel}>${lbl}</option>`;
      }
      input += '</select>';
    } else {
      input = `<input type="text" name="${name}" value="${editing ? (editing[name] || '') : ''}"
        ${isImage ? '' : 'required'}>`;
    }

    return `<label class="${isImage ? 'image-field' : ''}">
      ${label}
      ${input}
    </label>`;
  }).join('');

  return `
    <form class="admin-editor" id="editorForm">
      <h3>${isEdit ? 'Edit record' : 'Add record'}</h3>
      <div class="editor-fields">${fieldHtml}</div>
      ${isEdit ? `<input type="hidden" name="id" value="${editing.id}">` : ''}
      <button type="submit">${isEdit ? 'Update' : 'Save'}</button>
      ${isEdit ? `<button type="button" class="secondary" id="cancelEdit">Cancel</button>` : ''}
    </form>`;
}

/* ── Dashboard page ── */
async function initDashboard() {
  const user = await checkAuth();
  if (!user) return;

  const app = document.getElementById('app');
  app.innerHTML = `
    <div class="admin-shell">
      ${buildSidebar('dashboard')}
      <main>
        ${buildTopbar('Dashboard', 'dashboard')}
        <div id="content"><div class="admin-loading">Loading...</div></div>
      </main>
    </div>`;

  bindShellEvents();

  try {
    const data = await api('admin.php?resource=dashboard');
    document.getElementById('content').innerHTML = buildDashboard(data);
  } catch (e) {
    document.getElementById('content').innerHTML = `<div class="admin-error">${e.message}</div>`;
  }
}

/* ── Manage page ── */
let manageState = { section: 'appointments', editing: null, data: null };
const _uploadedUrls = {};

async function initManage() {
  const user = await checkAuth();
  if (!user) return;

  const params = new URLSearchParams(window.location.search);
  manageState.section = params.get('section') || 'appointments';

  renderManageShell();
  await loadData();
}

function renderManageShell() {
  const section = manageState.section;
  const current = SECTIONS.find(s => s[0] === section);
  const title = current ? current[1] : section;

  const options = SECTIONS.filter(s => s[0] !== 'dashboard')
    .map(s => `<option value="${s[0]}" ${s[0] === section ? 'selected' : ''}>${s[1]}</option>`)
    .join('');

  const app = document.getElementById('app');
  app.innerHTML = `
    <div class="admin-shell">
      ${buildSidebar(section)}
      <main>
        ${buildTopbar(title, section)}
        <div class="section-selector">
          <select id="sectionSelect">${options}</select>
        </div>
        <div id="toolbar"></div>
        <div id="editor"></div>
        <div id="content"><div class="admin-loading">Loading...</div></div>
      </main>
    </div>`;

  bindShellEvents();

  document.getElementById('sectionSelect').addEventListener('change', (e) => {
    window.location.href = `manage.html?section=${e.target.value}`;
  });
}

function bindShellEvents() {
  document.getElementById('logoutBtn').addEventListener('click', (e) => { e.preventDefault(); logout(); });
  document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
  const mt = document.getElementById('mobileToggle');
  if (mt) mt.addEventListener('click', () => document.querySelector('.admin-shell').classList.remove('sidebar-collapsed'));
}

/* ── Manage event delegation (set up once) ── */
document.addEventListener('click', (e) => {
  const addBtn = e.target.closest('#addBtn');
  if (addBtn) {
    manageState.editing = {};
    renderEditorOnly();
    return;
  }

  const editBtn = e.target.closest('.edit-btn');
  if (editBtn) {
    const id = editBtn.dataset.id;
    startEditing(id);
    return;
  }

  const deleteBtn = e.target.closest('.delete-btn');
  if (deleteBtn) {
    const id = deleteBtn.dataset.id;
    if (confirm('Delete this record?')) {
      mutate('DELETE', { id: +id });
    }
    return;
  }

  const clearBtn = e.target.closest('.file-clear');
  if (clearBtn) {
    const field = clearBtn.dataset.field;
    clearFile(field);
    return;
  }
});

document.addEventListener('change', (e) => {
  const sectionSelect = e.target.closest('#sectionSelect');
  if (sectionSelect) return;

  const statusSelect = e.target.closest('.status-select');
  if (statusSelect) {
    mutate('PATCH', { id: +statusSelect.dataset.id, status: statusSelect.value });
    return;
  }

  const fileInput = e.target.closest('.file-input');
  if (fileInput) {
    uploadFile(fileInput);
    return;
  }
});

document.addEventListener('dragover', (e) => {
  const zone = e.target.closest('.file-zone');
  if (zone) { e.preventDefault(); zone.classList.add('drag-over'); }
});

document.addEventListener('dragleave', (e) => {
  const zone = e.target.closest('.file-zone');
  if (zone) zone.classList.remove('drag-over');
});

document.addEventListener('drop', (e) => {
  const zone = e.target.closest('.file-zone');
  if (!zone) return;
  e.preventDefault();
  zone.classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file) uploadFileViaDrop(file, zone.dataset.field);
});

document.addEventListener('submit', (e) => {
  const form = e.target.closest('#editorForm');
  if (!form) return;
  e.preventDefault();
  saveForm(form);
});

document.addEventListener('input', (e) => {
  if (e.target.closest('[name="title_ne"]') && document.getElementById('slugInput')) {
    const slug = document.getElementById('slugInput');
    slug.value = e.target.value
      .toLowerCase().replace(/[^\w\s\-]/g, '').replace(/\s+/g, '-')
      .replace(/-+/g, '-').replace(/^-|-$/g, '') || 'post-' + Date.now();
  }
});

/* ── Manage helpers ── */
function renderEditorOnly() {
  document.getElementById('editor').innerHTML = buildEditor(manageState.section, {});
  document.getElementById('toolbar').innerHTML = '';
}

async function loadData() {
  manageState.data = null;
  manageState.editing = null;
  for (const k in _uploadedUrls) delete _uploadedUrls[k];
  const content = document.getElementById('content');
  content.innerHTML = '<div class="admin-loading">Loading...</div>';
  document.getElementById('toolbar').innerHTML = '';
  document.getElementById('editor').innerHTML = '';

  try {
    manageState.data = await api(`admin.php?resource=${manageState.section}`);
    renderManageContent();
  } catch (e) {
    content.innerHTML = `<div class="admin-error">${e.message}</div>`;
  }
}

function renderManageContent() {
  const section = manageState.section;
  const rows = Array.isArray(manageState.data) ? manageState.data : [];
  const hasEditor = !!EDITORS[section];

  document.getElementById('toolbar').innerHTML = hasEditor && !manageState.editing
    ? `<button class="button button-gold" id="addBtn">＋ Add ${section.replace(/_/g, ' ')}</button>`
    : '';

  document.getElementById('content').innerHTML = buildTable(rows, section);
}

function startEditing(id) {
  const rows = Array.isArray(manageState.data) ? manageState.data : [];
  const record = rows.find(r => r.id == id);
  if (!record) return;

  manageState.editing = record;
  document.getElementById('toolbar').innerHTML = '';
  document.getElementById('editor').innerHTML = buildEditor(manageState.section, record);
  document.getElementById('content').innerHTML = buildTable(rows, manageState.section);
}

async function saveForm(form) {
  const fd = new FormData(form);
  for (const [k, v] of Object.entries(_uploadedUrls)) {
    fd.set(k, v);
  }
  const payload = Object.fromEntries(fd);
  const isEdit = !!payload.id;

  try {
    await api(`admin.php?resource=${manageState.section}`, {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(payload),
    });
    manageState.editing = null;
    for (const k in _uploadedUrls) delete _uploadedUrls[k];
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

async function mutate(method, payload) {
  try {
    await api(`admin.php?resource=${manageState.section}`, {
      method,
      body: JSON.stringify(payload),
    });
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

/* ── File upload ── */
async function uploadFile(input) {
  const file = input.files[0];
  if (!file) return;
  const field = input.dataset.field;
  const zone = document.querySelector(`.file-zone[data-field="${field}"]`);
  const progress = document.querySelector(`.upload-progress`);

  try {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">⏳</span>Uploading...</span>';

    const fd = new FormData();
    fd.append('file', file);
    fd.append('type', 'general');

    const res = await fetch(`${API_BASE}/upload.php`, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd,
    });
    const d = await res.json();
    if (!d.success) throw new Error(d.message || 'Upload failed');

    const url = d.data.url;
    _uploadedUrls[field] = url;

    if (zone) zone.innerHTML = `<img src="${url}" alt="preview" class="file-preview">`;
  } catch (e) {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">⚠️</span>Upload failed. Try again.</span>';
  }
}

async function uploadFileViaDrop(file, field) {
  const zone = document.querySelector(`.file-zone[data-field="${field}"]`);

  try {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">⏳</span>Uploading...</span>';

    const fd = new FormData();
    fd.append('file', file);
    fd.append('type', 'general');

    const res = await fetch(`${API_BASE}/upload.php`, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd,
    });
    const d = await res.json();
    if (!d.success) throw new Error(d.message || 'Upload failed');

    const url = d.data.url;
    _uploadedUrls[field] = url;

    if (zone) zone.innerHTML = `<img src="${url}" alt="preview" class="file-preview">`;
  } catch (e) {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">⚠️</span>Upload failed.</span>';
  }
}

function clearFile(field) {
  const zone = document.querySelector(`.file-zone[data-field="${field}"]`);
  if (zone) zone.innerHTML = '<span><span class="upload-icon">☁️</span>Drop image here or click to upload</span>';
  const hidden = document.querySelector(`.file-hidden[data-field="${field}"]`);
  if (hidden) hidden.value = '';
  const clearBtn = document.querySelector(`.file-clear[data-field="${field}"]`);
  if (clearBtn) clearBtn.remove();
  delete _uploadedUrls[field];
}

/* ── Sidebar toggle ── */
function toggleSidebar() {
  document.querySelector('.admin-shell').classList.toggle('sidebar-collapsed');
}

/* ── Login page ── */
async function initLogin() {
  const form = document.getElementById('loginForm');
  const errorDiv = document.getElementById('loginError');

  try {
    await api('auth.php');
    window.location.href = 'dashboard.html';
    return;
  } catch {}

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.textContent = '';
    errorDiv.style.display = 'none';

    try {
      await api('auth.php', {
        method: 'POST',
        body: JSON.stringify({
          username: document.getElementById('username').value,
          password: document.getElementById('password').value,
        }),
      });
      window.location.href = 'dashboard.html';
    } catch (err) {
      errorDiv.textContent = err.message;
      errorDiv.style.display = 'block';
    }
  });
}
