# Admin Panel Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the Shreehari Jyotish PHP admin panel with a responsive, mobile-first layout, off-canvas sidebar drawer, data-table-to-cards conversion, and premium spiritual design system — preserving all backend PHP logic.

**Architecture:** Single CSS file (`assets/admin.css`) with CSS custom properties. Shared `header.php` provides sidebar + topbar shell. `footer.php` provides drawer JS + closing tags. Each PHP page uses shared component classes (`.sidebar`, `.data-table`, `.stat-card`, `.form-card`, `.badge`, `.mobile-card`). No framework, no bundler — pure CSS + vanilla JS.

**Tech Stack:** PHP (server-rendered), CSS3 (Grid, Flexbox, custom properties), vanilla JS (drawer, detail toggle, password toggle, confirm dialogs), Noto Sans Devanagari + Playfair Display fonts.

---

### Task 1: CSS Design System — `assets/admin.css`

**Files:**
- Modify: `assets/admin.css` — complete rewrite

- [ ] **Step 1: Write the complete CSS design system**

Replace the entire `assets/admin.css` with the new design system:

```css
/* ═══════════════════════════════════════════
   श्रीहरि ज्योतिष — Admin Panel CSS
   Responsive · Mobile-first · Accessible
   ═══════════════════════════════════════════ */

:root {
  --wine-950: #2d080d;
  --wine-900: #3b0e12;
  --wine-800: #54121b;
  --wine-700: #701d29;
  --gold-500: #c9902e;
  --gold-300: #e2bd72;
  --cream-100: #fbf7ef;
  --cream-200: #f4ecdd;
  --cream-300: #e9dcc8;
  --white: #fffefa;
  --ink: #2d2422;
  --muted: #776864;
  --line: #e7dccd;
  --line-light: #f0e8de;
  --green: #26704c;
  --amber: #9a5c09;
  --blue: #315d8c;
  --red: #a43b3b;
  --sidebar-w: 272px;
  --header-h: 64px;
  --header-h-mobile: 56px;
  --radius: 14px;
  --radius-sm: 10px;
  --radius-lg: 16px;
  --shadow: 0 14px 36px rgba(61, 11, 18, .07);
  --shadow-lg: 0 20px 48px rgba(61, 11, 18, .10);
  --shadow-xl: 0 24px 60px rgba(61, 11, 18, .14);
  --z-overlay: 40;
  --z-sidebar: 50;
  --z-header: 30;
  --z-modal: 60;
  --z-toast: 70;
  --font: 'Noto Sans Devanagari', 'Noto Sans', system-ui, -apple-system, sans-serif;
  --display: 'Playfair Display', Georgia, serif;
  --transition: .2s ease;
}

*, *::before, *::after { box-sizing: border-box; }
html { scroll-behavior: smooth; }

body {
  margin: 0; min-width: 0; padding-top: 0;
  font-family: var(--font); background: var(--cream-100); color: var(--ink);
  line-height: 1.5; -webkit-font-smoothing: antialiased;
}

a { color: inherit; text-decoration: none; }
svg { display: block; flex-shrink: 0; }

/* ── Reduced motion ── */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}

/* ── Scrollbar ── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--line); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--muted); }

/* ═══════════════════════════════════════════
   SIDEBAR (mobile drawer default)
   ═══════════════════════════════════════════ */

.sidebar {
  position: fixed; inset: 0 auto 0 0; z-index: var(--z-sidebar);
  width: min(86vw, 320px); display: flex; flex-direction: column; overflow: hidden;
  color: #f9edda;
  background: radial-gradient(circle at 10% 0%, rgba(201,144,46,.16), transparent 28%),
              linear-gradient(165deg, var(--wine-800), var(--wine-950));
  transform: translateX(-102%);
  transition: transform .26s ease;
  box-shadow: 24px 0 60px rgba(25,2,6,.28);
  padding-top: env(safe-area-inset-top);
}

.sidebar.open { transform: translateX(0); }

.sidebar-head {
  min-height: 82px; display: flex; align-items: center; gap: 12px;
  padding: 16px 14px 16px 20px; border-bottom: 1px solid rgba(255,255,255,.09);
}

.sidebar-brand {
  display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1;
  color: inherit; text-decoration: none;
}

.sidebar-brand:hover { color: inherit; text-decoration: none; }

.sidebar-logo {
  width: 44px; height: 44px; flex: 0 0 44px;
  display: grid; place-items: center;
  border: 1px solid rgba(255,255,255,.22); border-radius: 50%;
  color: var(--wine-950); background: var(--gold-300);
  font-family: Georgia, serif; font-size: 24px;
  box-shadow: inset 0 0 0 4px rgba(84,18,27,.08);
}

.sidebar-brand-text { min-width: 0; }

.sidebar-brand-name {
  display: block; font-family: var(--display); font-size: 17px;
  letter-spacing: .01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.sidebar-brand-sub {
  display: block; margin-top: 1px;
  color: rgba(249,237,218,.6); font-size: 10px; text-transform: uppercase; letter-spacing: .11em;
}

.sidebar-close {
  width: 44px; height: 44px; flex: 0 0 44px;
  display: grid; place-items: center; padding: 0;
  border: 0; border-radius: 11px; color: inherit; background: transparent; cursor: pointer;
}

.sidebar-close:hover { background: rgba(255,255,255,.08); }

.sidebar-close svg { width: 20px; height: 20px; }

/* ── Sidebar navigation ── */

.sidebar-scroll {
  min-height: 0; flex: 1; overflow-y: auto;
  padding: 14px 12px; scrollbar-width: thin;
}

.sidebar-nav { display: flex; flex-direction: column; }
.sidebar-nav + .sidebar-nav { margin-top: 18px; }

.sidebar-section {
  margin: 0 10px 7px;
  color: var(--gold-300); opacity: .72;
  font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
}

.sidebar-link {
  min-height: 48px; display: flex; align-items: center; gap: 12px;
  margin: 2px 0; padding: 10px 12px; border-radius: 11px;
  color: rgba(255,248,236,.78); text-decoration: none; font-size: 13px; font-weight: 600;
}

.sidebar-link svg { opacity: .84; flex-shrink: 0; }

.sidebar-link:hover { color: white; background: rgba(255,255,255,.07); text-decoration: none; }

.sidebar-link.active {
  position: relative; color: #2f0a0e;
  background: linear-gradient(135deg, #f5e2bd, #dfb667);
  box-shadow: 0 8px 22px rgba(20,0,3,.16);
}

.sidebar-link.active::before {
  content: ""; position: absolute; left: 6px;
  width: 3px; height: 20px; border-radius: 3px; background: var(--wine-700);
}

.sidebar-link-icon {
  width: 20px; height: 20px; flex-shrink: 0; display: grid; place-items: center;
}

.sidebar-link-icon svg { width: 20px; height: 20px; opacity: .84; }

.sidebar-link.active .sidebar-link-icon svg { opacity: 1; }

.sidebar-link-label {
  min-width: 0; flex: 1; overflow-wrap: anywhere;
}

.sidebar-badge {
  min-width: 22px; height: 22px; display: grid; place-items: center;
  padding: 0 6px; border-radius: 999px;
  color: #fff8ed; background: rgba(255,255,255,.13); font-size: 10px; font-weight: 700;
}

.sidebar-link.active .sidebar-badge { color: white; background: var(--wine-700); }

/* ── Sidebar footer ── */

.sidebar-footer {
  padding: 12px; padding-bottom: calc(12px + env(safe-area-inset-bottom));
  border-top: 1px solid rgba(255,255,255,.09);
}

.sidebar-admin-info {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 8px; padding: 10px 11px; border-radius: 11px;
  background: rgba(255,255,255,.055);
}

.sidebar-avatar {
  width: 36px; height: 36px; flex: 0 0 36px;
  display: grid; place-items: center; border-radius: 50%;
  color: var(--wine-900); background: #f0d59f; font-size: 12px; font-weight: 800;
}

.sidebar-admin-text { min-width: 0; flex: 1; }
.sidebar-admin-text strong, .sidebar-admin-text small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sidebar-admin-text strong { font-size: 12px; }
.sidebar-admin-text small { color: rgba(249,237,218,.58); font-size: 10px; }

.sidebar-footer .sidebar-link { min-height: 46px; font-size: 12px; }
.sidebar-footer .sidebar-link.logout-link { color: #ffc9c1; }

/* ── Sidebar overlay ── */

.sidebar-overlay {
  position: fixed; inset: 0; z-index: var(--z-overlay);
  visibility: hidden; opacity: 0;
  background: rgba(25,8,9,.56); backdrop-filter: blur(2px);
  transition: opacity .2s, visibility .2s;
}

.sidebar-overlay.active { visibility: visible; opacity: 1; }

/* ═══════════════════════════════════════════
   HEADER / TOPBAR
   ═══════════════════════════════════════════ */

.admin-header {
  position: sticky; top: 0; z-index: var(--z-header);
  min-height: var(--header-h-mobile);
  display: flex; align-items: center; gap: 10px;
  padding: max(8px, env(safe-area-inset-top)) 14px 8px;
  border-bottom: 1px solid var(--line);
  background: rgba(255,254,250,.92); backdrop-filter: blur(14px);
}

.header-left {
  display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;
}

.header-toggle {
  width: 44px; height: 44px; flex: 0 0 44px;
  display: grid; place-items: center; padding: 0;
  border: 0; border-radius: 11px; color: var(--wine-800); background: transparent; cursor: pointer;
}

.header-toggle:hover { background: var(--cream-200); }
.header-toggle:focus-visible { outline: 3px solid rgba(201,144,46,.45); outline-offset: 2px; }
.header-toggle svg { width: 22px; height: 22px; }

.header-title {
  min-width: 0; flex: 1;
}

.header-title h1 {
  margin: 0; overflow: hidden;
  color: var(--wine-900); font-size: 17px; line-height: 1.2; text-overflow: ellipsis; white-space: nowrap;
}

.header-breadcrumb {
  display: none; margin-top: 2px; color: var(--muted); font-size: 11px;
}

.header-right {
  display: flex; align-items: center; gap: 4px; flex-shrink: 0;
}

.header-btn {
  min-height: 40px; display: inline-flex; align-items: center; gap: 7px;
  padding: 0 11px; border: 1px solid var(--cream-300); border-radius: 11px;
  color: var(--wine-800); background: var(--cream-200);
  text-decoration: none; font-size: 12px; font-weight: 700;
  cursor: pointer; transition: all var(--transition);
}

.header-btn:hover { background: var(--cream-300); color: var(--wine-900); text-decoration: none; }
.header-btn:focus-visible { outline: 3px solid rgba(201,144,46,.45); outline-offset: 2px; }

.header-btn svg { width: 16px; height: 16px; }

.header-btn .btn-label { display: none; }

.header-profile {
  display: none; align-items: center; gap: 9px;
  margin-left: 5px; padding-left: 12px; border-left: 1px solid var(--line);
  min-height: 40px;
}

.header-profile .sidebar-avatar { width: 34px; height: 34px; flex-basis: 34px; }
.header-profile strong { font-size: 11px; display: block; }
.header-profile small { display: block; color: var(--muted); font-size: 9px; }

/* ═══════════════════════════════════════════
   MAIN LAYOUT
   ═══════════════════════════════════════════ */

.admin-wrapper {
  min-height: 100vh; display: flex; flex-direction: column;
}

.admin-main {
  width: 100%; max-width: 1560px; margin: 0 auto;
  padding: 18px 14px 72px; flex: 1;
}

/* ═══════════════════════════════════════════
   PAGE HEADER
   ═══════════════════════════════════════════ */

.page-header {
  display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;
}

.page-header-row {
  display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
}

.page-header h1 {
  margin: 0; color: var(--wine-900);
  font-family: var(--display); font-size: clamp(25px, 4vw, 36px); line-height: 1.14;
}

.page-header-actions { display: flex; align-items: center; gap: 8px; }

/* ═══════════════════════════════════════════
   DASHBOARD STATS
   ═══════════════════════════════════════════ */

.stats-grid {
  display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 24px;
}

.stat-card {
  min-width: 0; padding: 15px;
  border: 1px solid var(--line); border-radius: var(--radius);
  background: var(--white); box-shadow: 0 7px 20px rgba(61,11,18,.045);
  transition: box-shadow var(--transition), transform var(--transition);
}

.stat-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }

.stat-card-icon {
  width: 36px; height: 36px;
  display: grid; place-items: center; border-radius: 10px;
  color: var(--wine-800); background: var(--cream-200);
}

.stat-card-icon svg { width: 18px; height: 18px; }

.stat-card-body { min-width: 0; }

.stat-card-value {
  display: block; margin-top: 12px;
  color: var(--wine-900); font-size: 25px; font-weight: 800; line-height: 1;
}

.stat-card-label {
  display: block; margin-top: 6px;
  color: var(--muted); font-size: 11px; font-weight: 600;
  overflow-wrap: anywhere;
}

/* ═══════════════════════════════════════════
   SECTION TABS (Filter pills)
   ═══════════════════════════════════════════ */

.section-tabs {
  display: flex; gap: 4px; margin-bottom: 20px;
  background: var(--white); border-radius: var(--radius);
  padding: 6px; border: 1px solid var(--line);
  overflow-x: auto; -webkit-overflow-scrolling: touch;
}

.section-tab {
  padding: 8px 16px; border-radius: var(--radius-sm);
  font-size: 12px; font-weight: 600;
  color: var(--muted); white-space: nowrap; cursor: pointer;
  transition: all var(--transition);
  border: 0; background: transparent; font-family: var(--font);
  min-height: 36px; display: flex; align-items: center;
}

.section-tab:hover { background: var(--cream-100); color: var(--ink); }
.section-tab.active { background: var(--wine-800); color: white; }
.section-tab:focus-visible { outline: 3px solid rgba(201,144,46,.45); outline-offset: 2px; }

/* ═══════════════════════════════════════════
   DATA TABLE
   ═══════════════════════════════════════════ */

.data-table-wrap {
  background: var(--white); border-radius: var(--radius);
  box-shadow: var(--shadow); border: 1px solid var(--line);
  overflow-x: auto; -webkit-overflow-scrolling: touch;
}

.data-table {
  width: 100%; border-collapse: collapse; font-size: 12px;
}

.data-table thead { background: var(--wine-800); }

.data-table th {
  color: #f0e0c0; font-weight: 600;
  padding: 12px 14px; font-size: 10px; letter-spacing: .06em;
  text-align: left; white-space: nowrap; text-transform: uppercase;
  position: sticky; top: 0; z-index: 1;
}

.data-table td {
  padding: 13px 14px; border-top: 1px solid var(--line-light);
  color: var(--ink); vertical-align: middle; white-space: nowrap;
}

.data-table tbody tr { transition: background .1s; }
.data-table tbody tr:hover { background: #fffaf3; }

.data-table .cell-thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 8px; display: block; }
.data-table .cell-name { font-weight: 700; }
.data-table .cell-muted { color: var(--muted); font-size: 11px; }
.data-table .cell-amount { font-weight: 700; color: var(--wine-800); }

.data-table td.cell-actions {
  text-align: right; white-space: nowrap;
}

/* ── Action buttons ── */

.action-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border: 0;
  background: transparent; color: var(--muted);
  border-radius: var(--radius-sm); cursor: pointer;
  transition: all var(--transition); padding: 0;
}

.action-btn:hover { background: var(--cream-100); color: var(--wine-800); }
.action-btn:focus-visible { outline: 3px solid rgba(201,144,46,.45); outline-offset: 2px; }
.action-btn svg { width: 18px; height: 18px; }
.action-btn.danger:hover { background: #fef2f2; color: var(--red); }
.action-btn.primary:hover { background: rgba(201,144,46,.12); color: var(--wine-800); }

/* ═══════════════════════════════════════════
   MOBILE CARD LIST (table fallback)
   ═══════════════════════════════════════════ */

.mobile-records { padding: 12px; }

.mobile-record {
  padding: 14px; border: 1px solid var(--line);
  border-radius: 12px; background: var(--white);
}

.mobile-record + .mobile-record { margin-top: 10px; }

.mobile-record-header {
  display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;
}

.mobile-record-title { margin: 0; font-size: 14px; overflow-wrap: anywhere; }

.mobile-record-ref { display: block; margin-top: 2px; color: var(--muted); font-size: 10px; }

.mobile-record-body {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 13px;
}

.mobile-record-field span,
.mobile-record-field strong { display: block; overflow-wrap: anywhere; }

.mobile-record-field span { color: var(--muted); font-size: 10px; }
.mobile-record-field strong { margin-top: 1px; font-size: 11px; font-weight: 700; }

.mobile-record-footer {
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
  margin-top: 13px; padding-top: 12px; border-top: 1px solid var(--line);
}

/* ═══════════════════════════════════════════
   STATUS BADGES
   ═══════════════════════════════════════════ */

.badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 8px; border-radius: 999px;
  font-size: 10px; font-weight: 800; white-space: nowrap;
}

.badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

.badge-pending { color: var(--amber); background: #fff1cf; }
.badge-confirmed { color: var(--blue); background: #e7f0fa; }
.badge-completed { color: var(--green); background: #e4f3e9; }
.badge-cancelled { color: var(--red); background: #fdeaea; }
.badge-approved { color: var(--green); background: #e4f3e9; }
.badge-rejected { color: var(--red); background: #fdeaea; }
.badge-draft { color: #4b5563; background: #f3f4f6; }
.badge-published { color: var(--green); background: #e4f3e9; }
.badge-active { color: var(--green); background: #e4f3e9; }
.badge-inactive { color: #4b5563; background: #f3f4f6; }
.badge-new { color: var(--amber); background: #fff1cf; }
.badge-read { color: #4b5563; background: #f3f4f6; }
.badge-failed { color: var(--red); background: #fdeaea; }
.badge-processing { color: #3730a3; background: #e0e7ff; }
.badge-in_stock { color: var(--green); background: #e4f3e9; }
.badge-out_of_stock { color: var(--red); background: #fdeaea; }
.badge-pre_order { color: var(--amber); background: #fff1cf; }

/* ═══════════════════════════════════════════
   BUTTONS
   ═══════════════════════════════════════════ */

.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  padding: 10px 22px; border: 0; border-radius: var(--radius-sm);
  font-size: 13px; font-weight: 700; font-family: var(--font);
  cursor: pointer; transition: all var(--transition);
  min-height: 44px; white-space: nowrap;
}

.btn:focus-visible { outline: 3px solid rgba(201,144,46,.45); outline-offset: 2px; }

.btn-primary {
  background: linear-gradient(135deg, var(--wine-800), var(--wine-700));
  color: white;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(84,18,27,.25); }

.btn-secondary { background: var(--cream-100); color: var(--ink); border: 1px solid var(--line); }
.btn-secondary:hover { background: var(--cream-200); }

.btn-danger { background: var(--red); color: white; }
.btn-danger:hover { background: #8a2e2e; }

.btn-gold { background: linear-gradient(135deg, var(--gold-300), #c9942a); color: var(--wine-950); }
.btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(201,144,46,.3); }

.btn-outline { background: transparent; color: var(--muted); border: 1px solid var(--line); }
.btn-outline:hover { border-color: var(--wine-800); color: var(--wine-800); background: rgba(84,18,27,.03); }

.btn-sm { padding: 6px 14px; font-size: 11px; min-height: 36px; }
.btn-xs { padding: 4px 10px; font-size: 11px; min-height: 28px; border-radius: 6px; }

.btn-icon {
  width: 44px; height: 44px; padding: 0; display: grid; place-items: center;
}
.btn-icon svg { width: 20px; height: 20px; }

.btn-full { width: 100%; }

/* ═══════════════════════════════════════════
   FORMS
   ═══════════════════════════════════════════ */

.form-card {
  background: var(--white); border-radius: var(--radius);
  padding: 20px; box-shadow: var(--shadow); border: 1px solid var(--line);
}

.form-card h3 {
  margin: 0 0 16px; font-size: 15px; color: var(--wine-800);
  padding-bottom: 12px; border-bottom: 1px solid var(--line);
}

.form-grid {
  display: grid; grid-template-columns: 1fr; gap: 14px;
}

.form-grid .full { grid-column: 1 / -1; }

.form-field { display: flex; flex-direction: column; gap: 4px; }

.form-label {
  font-size: 11px; font-weight: 700; color: var(--ink);
  text-transform: uppercase; letter-spacing: .04em;
}

.form-input,
.form-textarea,
.form-select {
  padding: 10px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm);
  font-size: 13px; font-family: var(--font);
  background: var(--cream-100); color: var(--ink);
  transition: border-color var(--transition), box-shadow var(--transition);
  width: 100%;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none; border-color: var(--gold-500);
  box-shadow: 0 0 0 3px rgba(201,144,46,.15);
}

.form-input::placeholder { color: var(--muted); }
.form-input.error { border-color: var(--red); }
.form-error { font-size: 11px; color: var(--red); font-weight: 500; }

.form-actions {
  display: flex; gap: 12px; margin-top: 20px;
  padding-top: 16px; border-top: 1px solid var(--line-light);
}

.form-actions.sticky {
  position: sticky; bottom: 0; background: var(--white);
  padding: 16px 20px; margin: 20px -20px -20px;
  border-top: 1px solid var(--line); border-radius: 0 0 var(--radius) var(--radius);
}

/* ── Password wrap ── */

.password-wrap { position: relative; }
.password-wrap .form-input { padding-right: 44px !important; }

.password-toggle {
  position: absolute; right: 2px; top: 50%; transform: translateY(-50%);
  background: none; border: 0; padding: 8px 12px; cursor: pointer;
  color: var(--muted); display: flex; align-items: center; justify-content: center;
  transition: color var(--transition); border-radius: 4px;
}

.password-toggle:hover { color: var(--wine-800); background: var(--cream-100); }
.password-toggle svg { width: 20px; height: 20px; }

/* ── File upload ── */
.file-upload-wrap { position: relative; margin-top: 8px; }

.file-zone {
  border: 2px dashed var(--line); border-radius: var(--radius-sm);
  padding: 28px 16px; text-align: center; cursor: pointer;
  transition: all var(--transition); min-height: 100px;
  display: grid; place-items: center; background: var(--cream-100);
}

.file-zone.drag-over { border-color: var(--gold-500); background: #f8f0df; }

.file-zone span { display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; }
.file-zone .upload-icon { font-size: 28px; color: var(--gold-500); }
.file-preview { max-width: 100%; max-height: 140px; border-radius: var(--radius-sm); object-fit: contain; }

.file-clear {
  position: absolute; top: -8px; right: -8px; width: 24px; height: 24px;
  border-radius: 50%; border: 0; background: var(--wine-800); color: white;
  cursor: pointer; display: grid; place-items: center; font-size: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,.15); padding: 0;
}
.file-clear:hover { background: var(--wine-950); }

.upload-progress { font-size: 11px; color: var(--muted); margin-top: 4px; }

/* ═══════════════════════════════════════════
   ALERTS
   ═══════════════════════════════════════════ */

.alert {
  padding: 12px 16px; border-radius: var(--radius-sm);
  font-size: 13px; margin-bottom: 16px; font-weight: 500; line-height: 1.5;
}

.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert-info { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

/* ═══════════════════════════════════════════
   STATES
   ═══════════════════════════════════════════ */

.admin-loading { text-align: center; padding: 48px; color: var(--muted); }

.state-container {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 48px 24px; text-align: center;
}

.state-icon { width: 48px; height: 48px; color: var(--muted); margin-bottom: 16px; }
.state-icon svg { width: 48px; height: 48px; }
.state-title { font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
.state-text { font-size: 13px; color: var(--muted); margin-bottom: 20px; max-width: 360px; }
.state-error { color: var(--red); }

/* ═══════════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════════ */

.pagination {
  display: flex; align-items: center; justify-content: center;
  gap: 4px; padding: 16px;
}

.pagination-btn {
  width: 36px; height: 36px; border: 1px solid var(--line);
  border-radius: var(--radius-sm); background: var(--white);
  color: var(--ink); font-size: 13px; font-weight: 500;
  cursor: pointer; display: grid; place-items: center;
  transition: all var(--transition); padding: 0; font-family: var(--font);
}

.pagination-btn:hover { background: var(--cream-100); border-color: var(--muted); }
.pagination-btn.active { background: var(--wine-800); color: white; border-color: var(--wine-800); }
.pagination-btn:disabled { opacity: .4; cursor: not-allowed; }

/* ═══════════════════════════════════════════
   DETAIL GRID (expandable rows)
   ═══════════════════════════════════════════ */

.details-row td { padding: 16px 20px; background: #fcf8f2; }

.detail-grid { display: grid; grid-template-columns: 1fr; gap: 12px; font-size: 13px; }

.detail-grid div { padding: 4px 0; }
.detail-grid strong { color: var(--wine-800); font-size: 11px; text-transform: uppercase; letter-spacing: .04em; display: block; margin-bottom: 2px; }
.detail-grid textarea { width: 100%; padding: 8px 10px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 12px; font-family: var(--font); background: var(--cream-100); resize: vertical; }
.detail-grid select { padding: 6px 10px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 12px; background: var(--white); font-family: var(--font); }

.inline-form { width: 100%; }

/* ═══════════════════════════════════════════
   RECENT SECTION (Dashboard)
   ═══════════════════════════════════════════ */

.recent-section {
  background: var(--white); border-radius: var(--radius);
  padding: 20px; border: 1px solid var(--line); box-shadow: var(--shadow);
}

.recent-section h2 {
  margin: 0 0 14px; font-size: 14px; color: var(--wine-800);
  padding-bottom: 10px; border-bottom: 1px solid var(--line);
}

.list-item {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid var(--line-light); font-size: 13px;
}

.list-item:last-child { border-bottom: 0; }

.list-item-name { flex: 1; font-weight: 600; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.list-item-meta { color: var(--muted); font-size: 12px; white-space: nowrap; }

/* ═══════════════════════════════════════════
   LOGIN PAGE
   ═══════════════════════════════════════════ */

.login-page {
  min-height: 100vh; display: grid; place-items: center;
  background: linear-gradient(135deg, #f5f0e8 0%, #e8ddce 100%);
  padding: 20px;
}

.login-box {
  width: min(400px, calc(100% - 32px));
  background: var(--white); padding: 40px 36px 36px;
  border-radius: var(--radius-lg); box-shadow: var(--shadow-xl);
  border: 1px solid var(--line); text-align: center;
}

.login-logo {
  width: 64px; height: 64px; margin: 0 auto 12px;
  display: grid; place-items: center;
  border: 2px solid var(--gold-300); border-radius: 50%;
}

.login-box h1 { color: var(--wine-800); margin: 0 0 4px; font-size: 22px; }
.login-subtitle { color: var(--muted); font-size: 12px; margin: 0 0 28px; }

.login-box .form-field { text-align: left; margin-bottom: 18px; }
.login-box .form-label { font-size: 11px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; display: block; }

.login-box .form-input { display: block; width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 14px; font-family: var(--font); background: var(--cream-100); color: var(--ink); transition: border-color var(--transition), box-shadow var(--transition); }
.login-box .form-input:focus { outline: none; border-color: var(--gold-500); box-shadow: 0 0 0 3px rgba(201,144,46,.15); }

.login-options { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 13px; }

.remember-me { display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--ink); font-size: 13px; }
.remember-me input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--wine-800); cursor: pointer; }

.login-box .btn { width: 100%; padding: 13px; font-size: 15px; justify-content: center; margin-top: 4px; }
.login-box .back-link { display: block; margin-top: 20px; color: var(--muted); font-size: 12px; font-weight: 500; }
.login-box .back-link:hover { color: var(--wine-800); text-decoration: none; }

.login-box .alert-error {
  background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;
  padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 18px; text-align: center;
}

/* ═══════════════════════════════════════════
   RESPONSIVE — TABLET (640px+)
   ═══════════════════════════════════════════ */

@media (min-width: 640px) {
  .admin-main { padding: 24px 24px 80px; }
  .admin-header { padding-left: 24px; padding-right: 24px; }
  .header-title h1 { font-size: 19px; }
  .header-breadcrumb { display: block; }
  .header-btn .btn-label { display: inline; }
  .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
  .stat-card { padding: 18px; }
  .stat-card-value { font-size: 28px; }
  .form-grid { grid-template-columns: 1fr 1fr; }
  .detail-grid { grid-template-columns: 1fr 1fr; }
}

/* ═══════════════════════════════════════════
   RESPONSIVE — DESKTOP (1024px+)
   ═══════════════════════════════════════════ */

@media (min-width: 1024px) {
  .sidebar { width: var(--sidebar-w); max-width: none; transform: none; box-shadow: none; }
  .sidebar-close { display: none; }
  .sidebar-overlay { display: none !important; }
  .admin-wrapper { margin-left: var(--sidebar-w); }
  .admin-header { padding-left: 30px; padding-right: 30px; }
  .header-profile { display: flex; }
  .admin-main { padding: 30px; }
  .stats-grid { gap: 16px; }
  .stat-card { padding: 20px; }
  .stat-card-value { font-size: 32px; }
  .data-table th, .data-table td { padding: 14px 18px; }
  .section-tabs { padding: 8px; }
}

/* ═══════════════════════════════════════════
   RESPONSIVE — LARGE DESKTOP (1440px+)
   ═══════════════════════════════════════════ */

@media (min-width: 1440px) {
  .admin-main { padding: 34px 40px; }
}

/* ═══════════════════════════════════════════
   RESPONSIVE — SMALL MOBILE (≤374px)
   ═══════════════════════════════════════════ */

@media (max-width: 374px) {
  .stats-grid { grid-template-columns: 1fr; }
  .mobile-record-body { grid-template-columns: 1fr; }
}

/* ═══════════════════════════════════════════
   UTILITIES
   ═══════════════════════════════════════════ */

.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.text-muted { color: var(--muted); }
.text-wine { color: var(--wine-800); }
.text-sm { font-size: 12px; }
.font-semibold { font-weight: 600; }
.font-bold { font-weight: 700; }
.mt-4 { margin-top: 16px; }
.mb-4 { margin-bottom: 16px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.flex { display: flex; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.justify-end { justify-content: flex-end; }
.flex-1 { flex: 1; }
.min-width-0 { min-width: 0; }
```

- [ ] **Step 2: Verify CSS is valid and balanced**

Run: `node -e "const fs=require('fs');const c=fs.readFileSync('assets/admin.css','utf8');let n=0;for(const ch of c){if(ch==='{')n++;if(ch==='}')n--;if(n<0)throw Error('Unbalanced')}if(n)throw Error('Unbalanced: '+n);console.log('OK')"`

Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add assets/admin.css
git commit -m "feat: implement CSS design system for admin panel redesign"
```

---

### Task 2: Header.php — Sidebar + Topbar Shell

**Files:**
- Modify: `admin/includes/header.php`

- [ ] **Step 1: Rewrite header.php**

Replace the entire `header.php` with the new sidebar + topbar layout. Preserve all PHP session/auth/config logic. Add SVG icon sprite inline for navigation icons.

```php
<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../backend/includes/helpers.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}
validateCsrf();
$page = basename($_SERVER['PHP_SELF']);

$navMain = [
    ['page' => 'dashboard.php',    'label' => 'Dashboard',      'icon' => 'grid'],
    ['page' => 'appointments.php', 'label' => 'Appointments · परामर्श', 'icon' => 'calendar'],
    ['page' => 'pooja-orders.php', 'label' => 'Pooja Orders',    'icon' => 'flame'],
    ['page' => 'payments.php',     'label' => 'Payments',       'icon' => 'card'],
    ['page' => 'pooja-services.php','label' => 'Pooja Services', 'icon' => 'star'],
    ['page' => 'products.php',     'label' => 'Products',       'icon' => 'package'],
];

$navContent = [
    ['page' => 'articles.php',    'label' => 'Articles · लेखहरू', 'icon' => 'file'],
    ['page' => 'events.php',      'label' => 'Events & Tours', 'icon' => 'calendar'],
    ['page' => 'gallery.php',     'label' => 'Gallery',        'icon' => 'image'],
    ['page' => 'testimonials.php','label' => 'Testimonials',   'icon' => 'users'],
    ['page' => 'panchang.php',    'label' => 'Panchang · पञ्चाङ्ग', 'icon' => 'sun'],
    ['page' => 'messages.php',    'label' => 'Messages',       'icon' => 'mail'],
    ['page' => 'settings.php',    'label' => 'Settings',       'icon' => 'settings'],
];

$titles = [
    'dashboard.php' => 'Dashboard',
    'appointments.php' => 'Appointments',
    'pooja-orders.php' => 'Pooja Orders',
    'payments.php' => 'Payments',
    'products.php' => 'Products',
    'pooja-services.php' => 'Pooja Services',
    'articles.php' => 'Articles',
    'events.php' => 'Events & Tours',
    'gallery.php' => 'Gallery',
    'testimonials.php' => 'Testimonials',
    'panchang.php' => 'Panchang',
    'messages.php' => 'Messages',
    'settings.php' => 'Settings',
];

$icons = [
    'grid' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4m8-4v4M3 10h18"/></svg>',
    'flame' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22c4 0 7-3 7-7 0-5-4-7-3-12-4 2-8 6-8 11-1-1-2-2-2-4-2 2-2 4-2 6 0 4 3 6 8 6Z"/><path d="M10 19c-1-2 1-4 3-6 0 3 2 4 1 6"/></svg>',
    'card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>',
    'star' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 2 3 6 7 .9-5 4.8 1.2 6.8L12 17.3l-6.2 3.2L7 13.7 2 8.9 9 8l3-6Z"/></svg>',
    'package' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.5 9.4 7.5 4.2M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>',
    'file' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M9 12h6M9 16h6"/></svg>',
    'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>',
    'sun' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
    'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'x' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18"/></svg>',
    'menu' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
    'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 4h6v6M20 4 10 14"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/></svg>',
];

function renderNav($items, $currentPage, $icons) {
    $html = '';
    foreach ($items as $item) {
        $active = ($currentPage === $item['page']) ? ' active' : '';
        $html .= '<a href="' . BASE_URL . '/admin/' . $item['page'] . '" class="sidebar-link' . $active . '">';
        $html .= '<span class="sidebar-link-icon">' . ($icons[$item['icon']] ?? '') . '</span>';
        $html .= '<span class="sidebar-link-label">' . htmlspecialchars($item['label']) . '</span>';
        $html .= '</a>';
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titles[$page] ?? 'Admin') ?> — श्रीहरि ज्योतिष</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body>

<aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-head">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="sidebar-brand">
            <span class="sidebar-logo" aria-hidden="true">ॐ</span>
            <span class="sidebar-brand-text">
                <strong class="sidebar-brand-name">Shreehari Admin</strong>
                <small class="sidebar-brand-sub">Management System</small>
            </span>
        </a>
        <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Close navigation"><?= $icons['x'] ?></button>
    </div>
    <div class="sidebar-scroll">
        <nav class="sidebar-nav" aria-label="Main">
            <div class="sidebar-section">Main Navigation</div>
            <?= renderNav($navMain, $page, $icons) ?>
        </nav>
        <nav class="sidebar-nav" aria-label="Content">
            <div class="sidebar-section">Content Management</div>
            <?= renderNav($navContent, $page, $icons) ?>
        </nav>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-admin-info">
            <span class="sidebar-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) . strtoupper(substr($_SESSION['admin_name'] ?? 'D', 1, 1)) ?></span>
            <span class="sidebar-admin-text">
                <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                <small><?= htmlspecialchars($_SESSION['admin_role'] ?? 'administrator') ?></small>
            </span>
        </div>
        <a href="<?= BASE_URL ?>" target="_blank" rel="noreferrer" class="sidebar-link">
            <span class="sidebar-link-icon"><?= $icons['external'] ?></span>
            <span class="sidebar-link-label">View Website</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/index.php?logout=1" class="sidebar-link logout-link">
            <span class="sidebar-link-icon"><?= $icons['logout'] ?></span>
            <span class="sidebar-link-label">Logout · बाहिरिनुहोस्</span>
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">
    <header class="admin-header">
        <div class="header-left">
            <button class="header-toggle" id="sidebarToggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="sidebar"><?= $icons['menu'] ?></button>
            <div class="header-title">
                <h1><?= htmlspecialchars($titles[$page] ?? 'Admin') ?></h1>
                <div class="header-breadcrumb">Admin / <?= htmlspecialchars($titles[$page] ?? '') ?></div>
            </div>
        </div>
        <div class="header-right">
            <a href="<?= BASE_URL ?>" target="_blank" rel="noreferrer" class="header-btn">
                <?= $icons['external'] ?>
                <span class="btn-label">View Site</span>
            </a>
            <div class="header-profile">
                <span class="sidebar-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) . strtoupper(substr($_SESSION['admin_name'] ?? 'D', 1, 1)) ?></span>
                <div>
                    <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                    <small><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></small>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-main">
```

- [ ] **Step 2: Verify the file opens and closes properly**

Check that the file contains `<?php` at start and no unclosed PHP blocks.

- [ ] **Step 3: Commit**

```bash
git add admin/includes/header.php
git commit -m "feat: refactor header with new sidebar and topbar shell"
```

---

### Task 3: Footer.php — Drawer JS + Close Tags

**Files:**
- Modify: `admin/includes/footer.php`

- [ ] **Step 1: Rewrite footer.php**

```php
    </main>
</div>

<script>
(function(){
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');
    var closeBtn = document.getElementById('sidebarClose');
    var mainWrapper = document.querySelector('.admin-wrapper');
    var lastFocused = null;
    var isDesktop = function() { return window.matchMedia('(min-width: 1024px)').matches; };

    function openSidebar() {
        if (isDesktop()) return;
        lastFocused = document.activeElement;
        sidebar.classList.add('open');
        overlay.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 100);
    }

    function closeSidebar() {
        if (isDesktop()) return;
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        if (lastFocused) lastFocused.focus();
    }

    if (toggle) { toggle.addEventListener('click', function(e) { e.stopPropagation(); openSidebar(); }); }
    if (overlay) { overlay.addEventListener('click', closeSidebar); }
    if (closeBtn) { closeBtn.addEventListener('click', closeSidebar); }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) closeSidebar();
        if (e.key !== 'Tab' || !sidebar || !sidebar.classList.contains('open') || isDesktop()) return;
        var focusable = sidebar.querySelectorAll('a[href], button:not([disabled])');
        if (!focusable.length) return;
        if (e.shiftKey && document.activeElement === focusable[0]) { e.preventDefault(); focusable[focusable.length - 1].focus(); }
        else if (!e.shiftKey && document.activeElement === focusable[focusable.length - 1]) { e.preventDefault(); focusable[0].focus(); }
    });

    var links = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() { if (!isDesktop()) setTimeout(closeSidebar, 150); });
    }

    window.addEventListener('resize', function() { if (isDesktop() && sidebar) { sidebar.classList.remove('open'); if (overlay) overlay.classList.remove('active'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; } });
})();
</script>
</body>
</html>
```

- [ ] **Step 2: Commit**

```bash
git add admin/includes/footer.php
git commit -m "feat: add drawer interaction JS with focus trap and keyboard support"
```

---

### Task 4: Login Page

**Files:**
- Modify: `admin/index.php`

- [ ] **Step 1: Update login page HTML with new design classes**

Replace the `<body>` content with new layout. Keep all PHP form logic, CSRF, and POST handling intact. Key changes:
- Use `.login-page` on body
- Use `.login-box` container
- Use `.login-logo` div wrapping the SVG
- Use `.form-field`, `.form-label`, `.form-input` classes
- Keep `.password-wrap` and password toggle
- Use `.btn .btn-primary` on submit
- Use `.back-link` class
- Use `.alert-error` for error display

- [ ] **Step 2: Commit**

```bash
git add admin/index.php
git commit -m "feat: redesign login page with new design system"
```

---

### Task 5: Dashboard Page

**Files:**
- Modify: `admin/dashboard.php`

- [ ] **Step 1: Update dashboard.php to use new component classes**

Replace inline style blocks with:
- `.stats-grid` for the top stats cards
- `.stat-card`, `.stat-card-icon`, `.stat-card-body`, `.stat-card-value`, `.stat-card-label` for each stat
- `.recent-section` for the two recent lists (appointments + messages)
- `.list-item`, `.list-item-name`, `.list-item-meta` for list rows
- `.badge` classes for status badges
- Bottom 4 stats use `.stats-grid` with `.stat-card`

Keep ALL PHP query logic identical. Only change HTML structure + CSS classes.

- [ ] **Step 2: Commit**

```bash
git add admin/dashboard.php
git commit -m "feat: redesign dashboard page with stat cards and recent sections"
```

---

### Task 6: Appointments Page

**Files:**
- Modify: `admin/appointments.php`

- [ ] **Step 1: Update appointments page**

Replace HTML structure:
- `.page-header` with title
- `.section-tabs` for filter tabs (pending/confirmed/completed/cancelled/all)
- `.data-table-wrap` > `.data-table` for the table
- `.mobile-records` > `.mobile-record` cards for mobile fallback (hidden on desktop via CSS)
- `.details-row` > `.detail-grid` for expandable detail rows
- `.inline-form` for status update forms
- `.badge` classes for status
- `.btn` classes for action buttons
- Keep all PHP, form POST, CSRF intact
- Preserve `id="details-<?= $a['id'] ?>"` for JS toggle

- [ ] **Step 2: Commit**

```bash
git add admin/appointments.php
git commit -m "feat: redesign appointments page with responsive table and mobile cards"
```

---

### Task 7: Pooja Orders Page

**Files:**
- Modify: `admin/pooja-orders.php`

- [ ] **Step 1: Update pooja-orders.php**

Same pattern as appointments:
- `.section-tabs` for filter tabs
- `.data-table-wrap` > `.data-table`
- Mobile card fallback
- `.badge` classes
- Inline form for status update
- Keep all PHP logic

- [ ] **Step 2: Commit**

```bash
git add admin/pooja-orders.php
git commit -m "feat: redesign pooja orders page with responsive table"
```

---

### Task 8: Payments Page

**Files:**
- Modify: `admin/payments.php`

- [ ] **Step 1: Update payments.php**

Same pattern:
- `.section-tabs` for filter tabs
- `.data-table-wrap` > `.data-table`
- Mobile card fallback
- `.badge` for status
- Approve/reject inline forms with `.btn` classes
- Keep all PHP

- [ ] **Step 2: Commit**

```bash
git add admin/payments.php
git commit -m "feat: redesign payments page with responsive table"
```

---

### Task 9: Products Page

**Files:**
- Modify: `admin/products.php`

- [ ] **Step 1: Update products.php**

Pages with form + table side-by-side:
- Form uses `.form-card`, `.form-grid`, `.form-field`, `.form-label`, `.form-input/form-select/form-textarea`
- Table uses `.data-table-wrap` > `.data-table`
- Mobile: form stacks above table
- Action buttons: `.btn`, `.btn-sm`, `.btn-danger`
- `.badge` for stock status and active status
- Keep PHP POST handler, CSRF, edit/delete links intact

- [ ] **Step 2: Commit**

```bash
git add admin/products.php
git commit -m "feat: redesign products page with form + table layout"
```

---

### Task 10: Pooja Services Page

**Files:**
- Modify: `admin/pooja-services.php`

- [ ] **Step 1: Update pooja-services.php**

Same form + table pattern as products:
- `.form-card` for the editor
- `.data-table-wrap` > `.data-table` for the list
- Mobile: stacked layout
- Keep all PHP logic

- [ ] **Step 2: Commit**

```bash
git add admin/pooja-services.php
git commit -m "feat: redesign pooja services page with form + table layout"
```

---

### Task 11: Articles Page

**Files:**
- Modify: `admin/articles.php`

- [ ] **Step 1: Update articles.php**

Form + table pattern:
- `.form-card` for article editor (title, slug, content textareas, cover, etc.)
- `.data-table-wrap` > `.data-table` for article list
- Action buttons with `.btn`, `.btn-sm`, `.btn-danger`
- `.badge` for published status
- Keep PHP POST handler, slug generation, edit/delete

- [ ] **Step 2: Commit**

```bash
git add admin/articles.php
git commit -m "feat: redesign articles page with form + table layout"
```

---

### Task 12: Panchang Page

**Files:**
- Modify: `admin/panchang.php`

- [ ] **Step 1: Update panchang.php**

Form + table pattern:
- `.form-card` for the date/tithi/nakshatra editor
- Date picker with auto-calc display
- `.data-table-wrap` > `.data-table` for recent entries
- Keep PHP auto-calculation and upsert logic

- [ ] **Step 2: Commit**

```bash
git add admin/panchang.php
git commit -m "feat: redesign panchang page with form + table layout"
```

---

### Task 13: Messages Page

**Files:**
- Modify: `admin/messages.php`

- [ ] **Step 1: Update messages.php**

Table pattern:
- `.section-tabs` for all/unread filter
- `.data-table-wrap` > `.data-table` 
- Detail toggle row (`.details-row` with `.detail-grid`)
- Unread highlighting (amber dot + bold)
- `.badge` for read/new status
- Keep PHP query and mark-read/delete actions

- [ ] **Step 2: Commit**

```bash
git add admin/messages.php
git commit -m "feat: redesign messages page with responsive table"
```

---

### Task 14: Settings Page

**Files:**
- Modify: `admin/settings.php`

- [ ] **Step 1: Update settings.php**

Simple form page:
- `.form-card` (max-width 500px preferred)
- `.form-field`, `.form-label`, `.form-input` for password fields
- `.btn .btn-primary` for submit
- Keep PHP password validation/update logic

- [ ] **Step 2: Commit**

```bash
git add admin/settings.php
git commit -m "feat: redesign settings page with form card"
```

---

### Task 15: Polish — Events, Gallery, Testimonials Pages

The modules "Events & Tours", "Gallery", and "Testimonials" are listed in the sidebar navContent array but their PHP files don't yet exist in the `admin/` directory. When they are created in the future, follow the form + table or table-only patterns established above.

For now, ensure the sidebar links for these modules point to valid paths or are commented out if the pages don't exist.

- [ ] **Step 1: Verify pages exist for Events, Gallery, Testimonials**

```bash
ls admin/events.php admin/gallery.php admin/testimonials.php
```

If they don't exist, no action needed. The sidebar will link to them, returning a 404 or redirect. Users can add those pages later.

- [ ] **Step 2: If pages exist, update them following the established patterns**

---

### Task 16: Final Verification

- [ ] **Step 1: Check all CSS is balanced**

```bash
node -e "const fs=require('fs');const c=fs.readFileSync('assets/admin.css','utf8');let n=0;for(const ch of c){if(ch==='{')n++;if(ch==='}')n--;if(n<0)throw Error('Unbalanced')}if(n)throw Error('Unbalanced: '+n);console.log('CSS OK')"
```

- [ ] **Step 2: Verify PHP syntax on all modified files**

```bash
php -l admin/includes/header.php
php -l admin/includes/footer.php
php -l admin/dashboard.php
php -l admin/appointments.php
php -l admin/pooja-orders.php
php -l admin/payments.php
php -l admin/products.php
php -l admin/pooja-services.php
php -l admin/articles.php
php -l admin/panchang.php
php -l admin/messages.php
php -l admin/settings.php
php -l admin/index.php
```

All should output "No syntax errors detected".

- [ ] **Step 3: Visual check at all breakpoints**
Open any page in browser, use devtools to test:
- 320px – no horizontal scroll, sidebar off-canvas works, single-column stats
- 640px – 4-col stats, toolbar row, 2-col forms
- 820px – tables show instead of mobile card list
- 1024px – sidebar pinned, topbar with profile
- 1440px – larger content padding

- [ ] **Step 4: Verify all PHP functionality preserved**
- Login/logout works
- Dashboard stats load from DB
- Appointments filter tabs work
- Pooja orders status update works
- Payment approve/reject works
- Product CRUD works
- Article CRUD works
- Password change works

- [ ] **Step 5: Commit any final fixes**

```bash
git add -A
git commit -m "feat: finalize admin panel redesign"
```
