/* ═══════════════════════════════════════════
   श्रीहरि ज्योतिष — Admin Panel JS
   Vanilla JS · Responsive · Accessible
   ═══════════════════════════════════════════ */

/* ── Config ── */
var API_BASE = '/backend/api';

var SECTIONS = [
  ['dashboard',    'Dashboard',        'dashboard'],
  ['appointments', 'Appointments',     'appointments'],
  ['pooja',        'Pooja Orders',     'pooja'],
  ['payments',     'Payments',         'payments'],
  ['services',     'Pooja Services',   'services'],
  ['articles',     'Articles',         'articles'],
  ['events',       'Events & Tours',   'events'],
  ['gallery',      'Gallery',          'gallery'],
  ['testimonials', 'Testimonials',     'testimonials'],
  ['panchang',     'Panchang',         'panchang']
];

var MAIN_SECTIONS = ['dashboard', 'appointments', 'pooja', 'payments', 'services'];
var CONTENT_SECTIONS = ['articles', 'events', 'gallery', 'testimonials', 'panchang'];

var EDITORS = {
  services: [
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['category', 'Category'],
    ['base_price', 'Price'],
    ['duration_minutes', 'Duration (min)']
  ],
  articles: [
    ['title_ne', 'Nepali title'],
    ['slug', 'URL slug'],
    ['excerpt_ne', 'Excerpt'],
    ['content_ne', 'Content']
  ],
  panchang: [
    ['date', 'Date'],
    ['tithi', 'Tithi'],
    ['nakshatra', 'Nakshatra'],
    ['sunrise', 'Sunrise'],
    ['sunset', 'Sunset'],
    ['special_events_ne', 'Special events']
  ],
  testimonials: [
    ['name', 'Name'],
    ['title', 'Title'],
    ['content', 'Content'],
    ['rating', 'Rating (1–5)'],
    ['location', 'Location'],
    ['sort_order', 'Sort order']
  ],
  events: [
    ['type', 'Type (event/tour)'],
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['date_from', 'Date from'],
    ['location', 'Location'],
    ['contact_person', 'Contact person'],
    ['contact_phone', 'Contact phone']
  ],
  gallery: [
    ['type', 'Type (image/video/audio)'],
    ['title_ne', 'Nepali title'],
    ['url', 'URL'],
    ['thumbnail', 'Thumbnail URL'],
    ['embed_url', 'Embed URL'],
    ['source', 'Source']
  ]
};

var IMAGE_FIELDS = {
  articles: ['cover_image'],
  events: ['cover_image'],
  gallery: ['url', 'thumbnail'],
  testimonials: ['photo']
};

var STATUSES = {
  appointments: ['pending', 'confirmed', 'completed', 'cancelled'],
  pooja: ['pending', 'confirmed', 'completed', 'cancelled'],
  payments: ['pending', 'approved', 'rejected']
};

var SECTION_SELECTS = {
  events: { type: ['event', 'Event', 'tour', 'Tour'] }
};

/* ── SVG Icon set ── */
function icon(name) {
  var icons = {
    dashboard: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    appointments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>',
    pooja: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
    payments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="22" height="12" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/><circle cx="12" cy="14" r="2"/></svg>',
    services: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
    articles: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>',
    events: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>',
    gallery: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
    testimonials: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    panchang: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',
    view_site: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
    logout: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    menu: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
    x: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    plus: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    edit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    trash: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
    eye: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
    more_vertical: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>',
    check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
    arrow_up: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
    search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
    filter: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>',
    chevron_left: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>',
    chevron_right: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>',
    home: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    alert_circle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    refresh: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>'
  };
  return icons[name] || '';
}

/* ── API helper ── */
async function api(path, options) {
  if (!options) options = {};
  var response = await fetch(API_BASE + '/' + path, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    ...options
  });
  var body = await response.json().catch(function () {
    return { success: false, message: 'Server error (' + response.status + ')' };
  });
  if (!body.success) throw new Error(body.message || 'Request failed');
  return body.data;
}

/* ── Auth ── */
async function checkAuth() {
  try {
    return await api('auth.php');
  } catch (e) {
    window.location.href = 'login.html';
    return null;
  }
}

function logout() {
  api('auth.php?logout=1').then(function () {
    window.location.href = 'login.html';
  });
}

/* ── Sidebar management ── */
function isMobile() {
  return window.innerWidth < 1024;
}

function openSidebar() {
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  if (sidebar) sidebar.classList.add('open');
  if (overlay) overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
  // Focus trap: focus close button
  var closeBtn = document.querySelector('.sidebar-close');
  if (closeBtn) setTimeout(function () { closeBtn.focus(); }, 100);
}

function closeSidebar() {
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  if (sidebar) sidebar.classList.remove('open');
  if (overlay) overlay.classList.remove('active');
  document.body.style.overflow = '';
  // Return focus to toggle button
  var toggle = document.querySelector('.header-toggle');
  if (toggle) toggle.focus();
}

function toggleSidebar(e) {
  if (e) e.stopPropagation();
  if (isMobile()) {
    var sidebar = document.querySelector('.sidebar');
    if (sidebar && sidebar.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  } else {
    document.querySelector('.admin-shell').classList.toggle('sidebar-collapsed');
  }
}

/* ── Layout builders ── */
function buildSidebar(activeKey) {
  var mainNav = '';
  MAIN_SECTIONS.forEach(function (key) {
    var section = SECTIONS.find(function (s) { return s[0] === key; });
    if (!section) return;
    var activeClass = activeKey === key ? ' active' : '';
    mainNav += '<a href="' + (key === 'dashboard' ? 'dashboard.html' : 'manage.html?section=' + key) + '" class="sidebar-link' + activeClass + '" role="menuitem">' +
      '<span class="sidebar-link-icon">' + icon(key) + '</span>' +
      '<span class="sidebar-link-label">' + section[1] + '</span>' +
    '</a>';
  });

  var contentNav = '';
  CONTENT_SECTIONS.forEach(function (key) {
    var section = SECTIONS.find(function (s) { return s[0] === key; });
    if (!section) return;
    var activeClass = activeKey === key ? ' active' : '';
    contentNav += '<a href="manage.html?section=' + key + '" class="sidebar-link' + activeClass + '" role="menuitem">' +
      '<span class="sidebar-link-icon">' + icon(key) + '</span>' +
      '<span class="sidebar-link-label">' + section[1] + '</span>' +
    '</a>';
  });

  return '' +
    '<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">' +
      '<div class="sidebar-header">' +
        '<a href="dashboard.html" class="sidebar-brand">' +
          '<span class="sidebar-logo" aria-hidden="true">\u0950</span>' +
          '<div class="sidebar-brand-text">' +
            '<span class="sidebar-brand-name">Shreehari Admin</span>' +
            '<span class="sidebar-brand-sub">Management System</span>' +
          '</div>' +
        '</a>' +
        '<button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar menu">' + icon('x') + '</button>' +
      '</div>' +

      '<div class="sidebar-nav" role="menubar">' +
        '<div class="sidebar-group-label">Main Navigation</div>' +
        mainNav +
        '<div class="sidebar-group-label" style="padding-top:12px">Content Management</div>' +
        contentNav +
      '</div>' +

      '<div class="sidebar-footer">' +
        '<div class="sidebar-admin-info">' +
          '<span class="sidebar-admin-name" id="sidebarAdminName">Admin</span>' +
          '<span class="sidebar-admin-role" id="sidebarAdminRole">administrator</span>' +
        '</div>' +
        '<a href="/" target="_blank" rel="noreferrer" class="sidebar-link">' +
          '<span class="sidebar-link-icon">' + icon('view_site') + '</span>' +
          '<span class="sidebar-link-label">View Website</span>' +
        '</a>' +
        '<a href="#" id="logoutBtn" class="sidebar-link logout-link">' +
          '<span class="sidebar-link-icon">' + icon('logout') + '</span>' +
          '<span class="sidebar-link-label">Logout</span>' +
        '</a>' +
      '</div>' +
    '</aside>' +
    '<div class="sidebar-overlay" id="sidebarOverlay"></div>';
}

function buildHeader(title, sectionKey) {
  var current = SECTIONS.find(function (s) { return s[0] === sectionKey; });
  var displayTitle = title || (current ? current[1] : 'Dashboard');
  var sectionLabel = current ? current[1] : '';

  return '' +
    '<header class="admin-header" role="banner">' +
      '<div class="header-left">' +
        '<button class="header-toggle" id="sidebarToggle" aria-label="' + (isMobile() ? 'Open sidebar menu' : 'Toggle sidebar') + '" type="button">' +
          icon('menu') +
        '</button>' +
        '<h1 class="header-title">' + html(displayTitle) + '</h1>' +
        '<div class="header-breadcrumb" aria-label="Breadcrumb">' +
          '<span>' + icon('home') + '</span>' +
          '<span>\u203a</span>' +
          '<span>' + html(sectionLabel) + '</span>' +
        '</div>' +
      '</div>' +
      '<div class="header-right">' +
        '<a href="/" target="_blank" rel="noreferrer" class="header-btn view-site" aria-label="View website">' +
          icon('view_site') +
          '<span class="nav-label" style="display:none">View Site</span>' +
        '</a>' +
      '</div>' +
    '</header>';
}

/* ── Content builders ── */

function buildDashboard(data) {
  if (!data) return '<div class="state-container"><div class="loading-spinner"></div><p class="state-text" style="margin-top:16px">Loading dashboard...</p></div>';

  if (typeof data !== 'object' || Object.keys(data).length === 0) {
    return '<div class="state-container">' +
      '<div class="state-icon">' + icon('info') + '</div>' +
      '<p class="state-title">No data available</p>' +
      '<p class="state-text">Dashboard statistics will appear here once data is available.</p>' +
    '</div>';
  }

  var statLabels = {
    pending_appointments: 'Pending Appointments',
    today_appointments: 'Today\'s Appointments',
    pending_pooja: 'Pending Pooja Orders',
    pending_payments: 'Pending Payments',
    unread_messages: 'Unread Messages',
    active_services: 'Active Services',
    total_appointments: 'Total Appointments',
    total_articles: 'Published Articles',
    total_products: 'Store Products',
    total_pooja: 'Total Pooja Orders'
  };

  var cards = Object.entries(data).map(function (entry) {
    var key = entry[0];
    var val = entry[1];
    var label = statLabels[key] || key.replace(/_/g, ' ');
    return '<div class="stat-card">' +
      '<div class="stat-card-icon" aria-hidden="true">' + icon('dashboard') + '</div>' +
      '<div class="stat-card-body">' +
        '<span class="stat-card-value">' + val + '</span>' +
        '<span class="stat-card-label">' + html(label) + '</span>' +
      '</div>' +
    '</div>';
  }).join('');

  return '<div class="stats-grid">' + cards + '</div>';
}

function buildStatusBadge(status) {
  if (!status) return '';
  var s = String(status).toLowerCase().replace(/\s+/g, '_');
  return '<span class="badge badge-' + s + '">' + html(status) + '</span>';
}

function buildTable(rows, section) {
  if (!rows || !rows.length) {
    return '<div class="state-container">' +
      '<div class="state-icon">' + icon('info') + '</div>' +
      '<p class="state-title">No records yet</p>' +
      '<p class="state-text">No ' + (section || '') + ' records have been created yet.</p>' +
      (EDITORS[section] ? '<button class="btn btn-primary" id="addBtn">' + icon('plus') + ' Add ' + (section || '') + '</button>' : '') +
    '</div>';
  }

  var hidden = ['admin_notes', 'message', 'content_ne', 'content_en',
    'description_ne', 'description_en', 'password_hash', 'images',
    'excerpt_ne', 'excerpt_en', 'special_events_en', 'auspicious_times',
    'embed_url', 'source', 'sort_order', 'slug'];

  var keys = Object.keys(rows[0]).filter(function (k) {
    return hidden.indexOf(k) === -1;
  }).slice(0, 7);

  // Build thead
  var thead = keys.map(function (k) {
    var label = k.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });
    return '<th>' + html(label) + '</th>';
  }).join('');
  thead += '<th style="width:80px">Actions</th>';

  // Build tbody
  var tbody = rows.map(function (r) {
    var cells = keys.map(function (k) {
      var val = r[k];
      if (val === null || val === undefined) val = '\u2014';
      var label = k.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });

      // Handle images
      if (typeof val === 'string' && (val.startsWith('http') || val.startsWith('/assets')) &&
          (val.match(/\.(jpg|jpeg|png|gif|webp|svg)/i))) {
        return '<td data-label="' + html(label) + '"><img src="' + html(val) + '" alt="" class="cell-thumb" loading="lazy"></td>';
      }

      // Handle status fields
      if (k === 'status' || k === 'is_active' || k === 'is_read' || k === 'stock_status') {
        var badgeClass = String(val).toLowerCase().replace(/\s+/g, '_');
        if (k === 'is_active') badgeClass = val ? 'active' : 'inactive';
        if (k === 'is_read') badgeClass = val ? 'read' : 'new';
        var badgeLabel = val;
        if (k === 'is_active') badgeLabel = val ? 'Active' : 'Inactive';
        if (k === 'is_read') badgeLabel = val ? 'Read' : 'New';
        return '<td data-label="' + html(label) + '"><span class="badge badge-' + badgeClass + '">' + html(String(badgeLabel)) + '</span></td>';
      }

      // Handle price/amount
      if ((k === 'price' || k === 'base_price' || k === 'amount' || k === 'compare_price') && typeof val === 'number') {
        return '<td class="cell-amount" data-label="' + html(label) + '">\u0930\u0941 ' + Number(val).toLocaleString() + '</td>';
      }

      // Handle dates
      if (k === 'created_at' || k === 'published_at' || k === 'preferred_date' || k === 'date' || k === 'date_from') {
        return '<td class="cell-muted" data-label="' + html(label) + '">' + html(String(val)) + '</td>';
      }

      // Handle boolean/numbers that are short
      if (typeof val === 'boolean') {
        return '<td data-label="' + html(label) + '">' + (val ? 'Yes' : 'No') + '</td>';
      }

      // Handle long strings - truncate
      var display = String(val);
      if (display.length > 40) display = display.substring(0, 40) + '\u2026';

      // Name/title fields get bold
      if (k === 'name' || k === 'title_ne' || k === 'title_en' || k === 'user_name') {
        return '<td class="cell-name" data-label="' + html(label) + '">' + html(display) + '</td>';
      }

      return '<td data-label="' + html(label) + '">' + html(display) + '</td>';
    }).join('');

    var actions = '';
    var rId = r.id;

    // Status dropdown
    if (STATUSES[section]) {
      actions += '<select class="status-select" data-id="' + rId + '" data-section="' + section + '" aria-label="Change status" style="padding:4px 8px;border:1px solid var(--line);border-radius:4px;font-size:11px;font-family:var(--font);background:var(--card);color:var(--ink);margin-bottom:4px;width:100%;max-width:100px">';
      STATUSES[section].forEach(function (s) {
        var sel = r.status === s ? 'selected' : '';
        actions += '<option value="' + s + '" ' + sel + '>' + s.charAt(0).toUpperCase() + s.slice(1) + '</option>';
      });
      actions += '</select>';
    }

    // Edit/Delete buttons
    if (EDITORS[section]) {
      actions += '<div style="display:flex;gap:4px;margin-top:4px">' +
        '<button class="edit-btn action-btn primary" data-id="' + rId + '" aria-label="Edit record" title="Edit">' + icon('edit') + '</button>' +
        '<button class="delete-btn action-btn danger" data-id="' + rId + '" aria-label="Delete record" title="Delete">' + icon('trash') + '</button>' +
      '</div>';
    }

    return '<tr>' + cells + '<td class="cell-actions">' + actions + '</td></tr>';
  }).join('');

  if (tbody.length === 0) {
    return '<div class="state-container">' +
      '<div class="state-icon">' + icon('info') + '</div>' +
      '<p class="state-title">No records found</p>' +
      '<p class="state-text">No matching records exist for this filter.</p>' +
    '</div>';
  }

  return '' +
    '<div class="data-table-wrap">' +
      '<table class="data-table">' +
        '<thead><tr>' + thead + '</tr></thead>' +
        '<tbody>' + tbody + '</tbody>' +
      '</table>' +
    '</div>';
}

function buildEditor(section, editing) {
  var fields = EDITORS[section];
  if (!fields) return '';

  var isEdit = editing && editing.id;
  var selects = SECTION_SELECTS[section] || {};

  var fieldHtml = fields.map(function (f) {
    var name = f[0];
    var label = f[1];
    var isImage = IMAGE_FIELDS[section] && IMAGE_FIELDS[section].indexOf(name) !== -1;
    var isSlug = name === 'slug';
    var isSelect = selects[name];
    var val = editing ? (editing[name] || '') : '';
    var input = '';

    if (isImage) {
      input = '<div class="file-upload-wrap" data-field="' + name + '">' +
        '<div class="file-zone" data-field="' + name + '" role="button" tabindex="0" aria-label="Upload ' + label + '">' +
          (val ? '<img src="' + html(val) + '" alt="preview" class="file-preview">' :
            '<span><span class="upload-icon">\u2601\uFE0F</span>Drop image here or click to upload</span>') +
        '</div>' +
        '<input type="file" accept="image/*" style="display:none" class="file-input" data-field="' + name + '" aria-hidden="true">' +
        (val ? '<button type="button" class="file-clear" data-field="' + name + '" aria-label="Clear image">\u2715</button>' : '') +
        '<input type="hidden" name="' + name + '" value="' + html(val) + '" class="file-hidden">' +
      '</div>' +
      '<div class="upload-progress" style="display:none"></div>';
    } else if (isSlug) {
      input = '<input type="text" name="' + name + '" value="' + html(val) + '" placeholder="auto-generated" id="slugInput" class="form-input">';
    } else if (isSelect) {
      var opts = isSelect;
      input = '<select name="' + name + '" class="form-select">';
      input += '<option value="">\u2014 Select \u2014</option>';
      for (var i = 0; i < opts.length; i += 2) {
        var optVal = opts[i];
        var optLbl = opts[i + 1];
        var sel = editing && editing[name] === optVal ? 'selected' : '';
        input += '<option value="' + optVal + '" ' + sel + '>' + optLbl + '</option>';
      }
      input += '</select>';
    } else if (name.indexOf('content') !== -1 || name.indexOf('description') !== -1 || name.indexOf('excerpt') !== -1) {
      input = '<textarea name="' + name + '" class="form-textarea" rows="5">' + html(val) + '</textarea>';
    } else if (name.indexOf('date') !== -1 && val) {
      input = '<input type="text" name="' + name + '" value="' + html(val) + '" class="form-input">';
    } else {
      input = '<input type="text" name="' + name + '" value="' + html(val) + '" class="form-input" required>';
    }

    var cls = isImage ? 'image-field' : '';
    return '<div class="form-field ' + cls + '">' +
      '<label class="form-label" for="field-' + name + '">' + label + '</label>' +
      input +
    '</div>';
  }).join('');

  return '' +
    '<div class="form-card" style="margin-bottom:20px">' +
      '<form id="editorForm">' +
        '<h3>' + (isEdit ? 'Edit ' + section : 'Add ' + section) + '</h3>' +
        '<div class="form-grid">' + fieldHtml + '</div>' +
        (isEdit ? '<input type="hidden" name="id" value="' + editing.id + '">' : '') +
        '<div class="form-actions">' +
          '<button type="submit" class="btn btn-primary">' + icon('check') + ' ' + (isEdit ? 'Update' : 'Save') + '</button>' +
          (isEdit ? '<button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>' : '') +
        '</div>' +
      '</form>' +
    '</div>';
}

/* ── Manage page helpers ── */
var manageState = { section: 'appointments', editing: null, data: null };
var _uploadedUrls = {};

function renderManageShell() {
  var section = manageState.section;
  var current = SECTIONS.find(function (s) { return s[0] === section; });
  var title = current ? current[1] : section;

  var app = document.getElementById('app');
  app.innerHTML = '' +
    '<div class="admin-shell" id="adminShell">' +
      buildSidebar(section) +
      buildHeader(title, section) +
      '<div class="admin-shell-main">' +
        '<main class="admin-main" role="main">' +
          '<div class="page-header">' +
            '<div class="page-header-row">' +
              '<h1>' + html(title) + '</h1>' +
              '<div class="page-header-actions">' +
                '<div class="section-tabs" id="sectionTabs"></div>' +
              '</div>' +
            '</div>' +
          '</div>' +
          '<div id="toolbar"></div>' +
          '<div id="editor"></div>' +
          '<div id="content"><div class="state-container">' +
            '<div class="loading-spinner"></div>' +
            '<p class="state-text" style="margin-top:16px">Loading...</p>' +
          '</div></div>' +
        '</main>' +
      '</div>' +
    '</div>';

  bindShellEvents();

  // Build section tabs
  var tabsContainer = document.getElementById('sectionTabs');
  if (tabsContainer) {
    var tabsHtml = '';
    SECTIONS.forEach(function (s) {
      if (s[0] === 'dashboard') return;
      var active = s[0] === section ? ' active' : '';
      tabsHtml += '<a href="manage.html?section=' + s[0] + '" class="section-tab' + active + '">' + s[1] + '</a>';
    });
    tabsContainer.innerHTML = tabsHtml;
  }
}

function renderEditorOnly() {
  var editor = document.getElementById('editor');
  if (editor) editor.innerHTML = buildEditor(manageState.section, {});
  var toolbar = document.getElementById('toolbar');
  if (toolbar) toolbar.innerHTML = '';
}

async function loadData() {
  manageState.data = null;
  manageState.editing = null;
  for (var k in _uploadedUrls) delete _uploadedUrls[k];

  var content = document.getElementById('content');
  if (content) content.innerHTML = '<div class="state-container"><div class="loading-spinner"></div><p class="state-text" style="margin-top:16px">Loading...</p></div>';

  var toolbar = document.getElementById('toolbar');
  if (toolbar) toolbar.innerHTML = '';

  var editor = document.getElementById('editor');
  if (editor) editor.innerHTML = '';

  try {
    manageState.data = await api('admin.php?resource=' + manageState.section);
    renderManageContent();
  } catch (e) {
    if (content) {
      content.innerHTML = '' +
        '<div class="state-container">' +
          '<div class="state-icon state-error">' + icon('alert_circle') + '</div>' +
          '<p class="state-title state-error">Failed to load data</p>' +
          '<p class="state-text">' + html(e.message) + '</p>' +
          '<button class="btn btn-primary" onclick="loadData()">' + icon('refresh') + ' Retry</button>' +
        '</div>';
    }
  }
}

function renderManageContent() {
  var section = manageState.section;
  var rows = Array.isArray(manageState.data) ? manageState.data : [];
  var hasEditor = !!EDITORS[section];

  var toolbar = document.getElementById('toolbar');
  if (toolbar) {
    if (hasEditor && !manageState.editing) {
      toolbar.innerHTML = '<button class="btn btn-gold" id="addBtn">' + icon('plus') + ' Add ' + section.replace(/_/g, ' ') + '</button>';
    } else {
      toolbar.innerHTML = '';
    }
  }

  var content = document.getElementById('content');
  if (content) content.innerHTML = buildTable(rows, section);
}

function startEditing(id) {
  var rows = Array.isArray(manageState.data) ? manageState.data : [];
  var record = rows.find(function (r) { return r.id == id; });
  if (!record) return;

  manageState.editing = record;

  var toolbar = document.getElementById('toolbar');
  if (toolbar) toolbar.innerHTML = '';

  var editor = document.getElementById('editor');
  if (editor) editor.innerHTML = buildEditor(manageState.section, record);

  var content = document.getElementById('content');
  if (content) content.innerHTML = buildTable(rows, manageState.section);
}

async function saveForm(form) {
  var fd = new FormData(form);
  for (var k in _uploadedUrls) {
    fd.set(k, _uploadedUrls[k]);
  }
  var payload = {};
  fd.forEach(function (v, key) { payload[key] = v; });
  var isEdit = !!payload.id;

  try {
    await api('admin.php?resource=' + manageState.section, {
      method: isEdit ? 'PUT' : 'POST',
      body: JSON.stringify(payload)
    });
    manageState.editing = null;
    for (var k in _uploadedUrls) delete _uploadedUrls[k];
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

async function mutate(method, payload) {
  try {
    await api('admin.php?resource=' + manageState.section, {
      method: method,
      body: JSON.stringify(payload)
    });
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

/* ── Event binding ── */
function bindShellEvents() {
  // Sidebar toggle
  var toggle = document.getElementById('sidebarToggle');
  if (toggle) {
    toggle.addEventListener('click', toggleSidebar);
  }

  // Sidebar close
  var closeBtn = document.getElementById('sidebarClose');
  if (closeBtn) {
    closeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      closeSidebar();
    });
  }

  // Overlay
  var overlay = document.getElementById('sidebarOverlay');
  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Logout
  var logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function (e) {
      e.preventDefault();
      logout();
    });
  }

  // Sidebar link clicks on mobile
  var sidebarLinks = document.querySelectorAll('.sidebar a');
  for (var i = 0; i < sidebarLinks.length; i++) {
    sidebarLinks[i].addEventListener('click', function () {
      if (isMobile()) {
        setTimeout(closeSidebar, 150);
      }
    });
  }
}

/* ── Global event delegation ── */
document.addEventListener('click', function (e) {
  // Add button
  var addBtn = e.target.closest('#addBtn');
  if (addBtn) {
    manageState.editing = {};
    renderEditorOnly();
    return;
  }

  // Edit button
  var editBtn = e.target.closest('.edit-btn');
  if (editBtn) {
    var id = editBtn.dataset.id;
    startEditing(id);
    return;
  }

  // Delete button
  var deleteBtn = e.target.closest('.delete-btn');
  if (deleteBtn) {
    var id = deleteBtn.dataset.id;
    if (confirm('Delete this record? This action cannot be undone.')) {
      mutate('DELETE', { id: +id });
    }
    return;
  }

  // File clear
  var clearBtn = e.target.closest('.file-clear');
  if (clearBtn) {
    var field = clearBtn.dataset.field;
    clearFile(field);
    return;
  }

  // Cancel edit
  var cancelBtn = e.target.closest('#cancelEdit');
  if (cancelBtn) {
    manageState.editing = null;
    renderManageContent();
    return;
  }
});

document.addEventListener('change', function (e) {
  // Status select
  var statusSelect = e.target.closest('.status-select');
  if (statusSelect) {
    mutate('PATCH', { id: +statusSelect.dataset.id, status: statusSelect.value });
    return;
  }

  // File input
  var fileInput = e.target.closest('.file-input');
  if (fileInput) {
    uploadFile(fileInput);
    return;
  }
});

document.addEventListener('dragover', function (e) {
  var zone = e.target.closest('.file-zone');
  if (zone) { e.preventDefault(); zone.classList.add('drag-over'); }
});

document.addEventListener('dragleave', function (e) {
  var zone = e.target.closest('.file-zone');
  if (zone) zone.classList.remove('drag-over');
});

document.addEventListener('drop', function (e) {
  var zone = e.target.closest('.file-zone');
  if (!zone) return;
  e.preventDefault();
  zone.classList.remove('drag-over');
  var file = e.dataTransfer.files[0];
  if (file) uploadFileViaDrop(file, zone.dataset.field);
});

document.addEventListener('submit', function (e) {
  var form = e.target.closest('#editorForm');
  if (!form) return;
  e.preventDefault();
  saveForm(form);
});

document.addEventListener('input', function (e) {
  if (e.target.closest('[name="title_ne"]') && document.getElementById('slugInput')) {
    var slug = document.getElementById('slugInput');
    slug.value = e.target.value
      .toLowerCase().replace(/[^\w\s\-]/g, '').replace(/\s+/g, '-')
      .replace(/-+/g, '-').replace(/^-|-$/g, '') || 'post-' + Date.now();
  }
});

/* ── Keyboard: Escape closes sidebar ── */
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    if (document.querySelector('.sidebar.open')) {
      closeSidebar();
    }
  }
});

/* ── Window resize: handle sidebar state ── */
window.addEventListener('resize', function () {
  if (!isMobile()) {
    closeSidebar();
  }
});

/* ── File upload ── */
async function uploadFile(input) {
  var file = input.files[0];
  if (!file) return;
  var field = input.dataset.field;
  var zone = document.querySelector('.file-zone[data-field="' + field + '"]');
  var progress = document.querySelector('.upload-progress');

  try {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">\u23F3</span>Uploading...</span>';
    if (progress) { progress.style.display = 'block'; progress.textContent = 'Uploading...'; }

    var fd = new FormData();
    fd.append('file', file);
    fd.append('type', 'general');

    var res = await fetch(API_BASE + '/upload.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: fd
    });
    var d = await res.json();
    if (!d.success) throw new Error(d.message || 'Upload failed');

    var url = d.data.url;
    _uploadedUrls[field] = url;

    if (zone) zone.innerHTML = '<img src="' + url + '" alt="preview" class="file-preview">';
    if (progress) { progress.textContent = 'Upload complete'; setTimeout(function () { progress.style.display = 'none'; }, 2000); }
  } catch (e) {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">\u26A0\uFE0F</span>Upload failed. Try again.</span>';
    if (progress) { progress.textContent = 'Upload failed'; progress.style.color = '#b33a3a'; }
  }
}

async function uploadFileViaDrop(file, field) {
  var zone = document.querySelector('.file-zone[data-field="' + field + '"]');

  try {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">\u23F3</span>Uploading...</span>';

    var fd = new FormData();
    fd.append('file', file);
    fd.append('type', 'general');

    var res = await fetch(API_BASE + '/upload.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: fd
    });
    var d = await res.json();
    if (!d.success) throw new Error(d.message || 'Upload failed');

    var url = d.data.url;
    _uploadedUrls[field] = url;

    if (zone) zone.innerHTML = '<img src="' + url + '" alt="preview" class="file-preview">';
  } catch (e) {
    if (zone) zone.innerHTML = '<span><span class="upload-icon">\u26A0\uFE0F</span>Upload failed.</span>';
  }
}

function clearFile(field) {
  var zone = document.querySelector('.file-zone[data-field="' + field + '"]');
  if (zone) zone.innerHTML = '<span><span class="upload-icon">\u2601\uFE0F</span>Drop image here or click to upload</span>';
  var hidden = document.querySelector('.file-hidden[data-field="' + field + '"]');
  if (hidden) hidden.value = '';
  var clearBtn = document.querySelector('.file-clear[data-field="' + field + '"]');
  if (clearBtn) clearBtn.remove();
  delete _uploadedUrls[field];
}

/* ── HTML escape helper ── */
function html(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/* ═══════════════════════════════════════════
   PAGE INITIALIZERS
   ═══════════════════════════════════════════ */

/* ── Dashboard page ── */
async function initDashboard() {
  var user = await checkAuth();
  if (!user) return;

  var app = document.getElementById('app');
  app.innerHTML = '' +
    '<div class="admin-shell" id="adminShell">' +
      buildSidebar('dashboard') +
      buildHeader('Dashboard', 'dashboard') +
      '<div class="admin-shell-main">' +
        '<main class="admin-main" role="main">' +
          '<div id="dashboardContent"><div class="state-container">' +
            '<div class="loading-spinner"></div>' +
            '<p class="state-text" style="margin-top:16px">Loading dashboard...</p>' +
          '</div></div>' +
        '</main>' +
      '</div>' +
    '</div>';

  bindShellEvents();

  // Set admin name from session
  if (user && user.name) {
    var nameEl = document.getElementById('sidebarAdminName');
    if (nameEl) nameEl.textContent = user.name;
  }

  try {
    var data = await api('admin.php?resource=dashboard');
    var content = document.getElementById('dashboardContent');
    if (content) content.innerHTML = buildDashboard(data);
  } catch (e) {
    var content = document.getElementById('dashboardContent');
    if (content) {
      content.innerHTML = '' +
        '<div class="state-container">' +
          '<div class="state-icon state-error">' + icon('alert_circle') + '</div>' +
          '<p class="state-title state-error">Failed to load dashboard</p>' +
          '<p class="state-text">' + html(e.message) + '</p>' +
          '<button class="btn btn-primary" onclick="initDashboard()">' + icon('refresh') + ' Retry</button>' +
        '</div>';
    }
  }
}

/* ── Manage page ── */
async function initManage() {
  var user = await checkAuth();
  if (!user) return;

  var params = new URLSearchParams(window.location.search);
  manageState.section = params.get('section') || 'appointments';

  renderManageShell();

  // Set admin name
  if (user && user.name) {
    var nameEl = document.getElementById('sidebarAdminName');
    if (nameEl) nameEl.textContent = user.name;
  }

  await loadData();
}

/* ── Login page ── */
async function initLogin() {
  var form = document.getElementById('loginForm');
  var errorDiv = document.getElementById('loginError');

  try {
    await api('auth.php');
    window.location.href = 'dashboard.html';
    return;
  } catch (e) { /* Not logged in, show form */ }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (errorDiv) {
      errorDiv.textContent = '';
      errorDiv.style.display = 'none';
    }

    try {
      await api('auth.php', {
        method: 'POST',
        body: JSON.stringify({
          username: document.getElementById('username').value,
          password: document.getElementById('password').value
        })
      });
      window.location.href = 'dashboard.html';
    } catch (err) {
      if (errorDiv) {
        errorDiv.textContent = err.message;
        errorDiv.style.display = 'block';
      }
    }
  });
}
