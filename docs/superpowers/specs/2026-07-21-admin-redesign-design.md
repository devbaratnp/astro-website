# Shreehari Admin Panel Redesign — Design Specification

## Overview

Redesign the PHP-based Shreehari Jyotish admin panel (`admin/*.php`) to a professional, responsive, mobile-first administration system. Preserve all backend PHP logic, routes, database queries, form submissions, CSRF tokens, and session-based authentication. Improve only the frontend HTML structure, CSS, and layout.

## Current Architecture

The admin panel has two independent frontend implementations:

1. **PHP-server-rendered pages** (`admin/*.php`) — use `assets/admin.css` and `admin/includes/header.php` / `footer.php`. These are the authenticated pages used daily. Modules: dashboard, appointments, pooja-orders, payments, products, pooja-services, articles, panchang, messages, settings.

2. **API-driven JS-rendered pages** (`admin/dashboard.html`, `admin/manage.html`) — use `admin/css/style.css` and `admin/js/app.js`. These are a newer, partially implemented API-driven panel that uses `backend/api/admin.php`. Only two pages exist; not all modules are covered.

**Target for redesign:** The PHP-server-rendered pages (`admin/*.php`) because:
- They contain all modules and are the primary working panel
- The user brief lists modules matching the PHP panel exactly
- The JS-rendered pages are incomplete (missing products, messages, settings)
- PHP backend must remain unchanged

## Design Tokens

### Color System
```
--wine-950: #2d080d
--wine-900: #3b0e12  
--wine-800: #54121b
--wine-700: #701d29
--gold-500: #c9902e
--gold-300: #e2bd72
--cream-100: #fbf7ef
--cream-200: #f4ecdd
--cream-300: #e9dcc8
--white: #fffefa
--ink: #2d2422
--muted: #776864
--line: #e7dccd
--line-light: #f0e8de
--green: #26704c
--amber: #9a5c09
--blue: #315d8c
--red: #a43b3b
```

### Typography
- Font stack: `'Noto Sans Devanagari', 'Noto Sans', system-ui, -apple-system, sans-serif`
- Display font (headings): `'Playfair Display', Georgia, serif`
- Base: 14px–16px body; 11px–13px table/label; clamp-based headings

### Spacing & Radius
- Sidebar width: 272px (desktop), 86vw (mobile, max 320px)  
- Header height: 64px desktop, 56px mobile
- Card radius: 14px; Button/input radius: 10px
- Internal card padding: 18px–24px desktop, 14px–16px mobile

### Shadows
```
--shadow: 0 14px 36px rgba(61, 11, 18, .07)
--shadow-lg: 0 20px 48px rgba(61, 11, 18, .10)
```

### Z-index layers
```
--z-overlay: 40
--z-sidebar: 50
--z-header: 30
--z-modal: 60
--z-toast: 70
```

## Layout Architecture

### Desktop (≥1024px)
- Fixed left sidebar, 272px wide, full viewport height
- Brand/logo + "Shreehari Admin / Management System" at top
- Navigation grouped under "Main Navigation" and "Content Management" captions
- Profile (avatar + name/role) + View Website + Logout pinned to bottom
- Main content area with sticky topbar: menu toggle (desktop: collapse sidebar), page title, breadcrumb, View Site button, desktop profile
- Content below topbar: page-specific content, max-width 1560px, centered

### Tablet (640px–1023px)  
- Sidebar hidden by default, opens as off-canvas drawer
- Topbar shows hamburger menu, title, breadcrumb, site button
- Dashboard stats: 4-column grid
- Tables: horizontal scroll or card conversion at 820px threshold
- Forms: 2-column layout okay

### Mobile (<640px)
- Sidebar: off-canvas drawer covering 86vw (max 320px)
- Semi-transparent backdrop with blur
- Close via: close button, backdrop click, Escape key, nav link click
- Body scroll locked while open; focus trapped inside drawer
- Focus returns to menu toggle on close
- Topbar: compact (56px), hamburger + title, View Site as icon-only
- Dashboard stats: 2-column grid; collapses to 1 column at ≤374px
- Tables: fully converted to stacked record cards with `data-label`
- Forms: single-column stacked layout
- Pagination: centered compact pill

### Breakpoints
```
320px  — minimum supported; single-column stats, condensed cards
374px  — stats go 1-col
640px  — tablet: 4-col stats, toolbar row, form 2-col
820px  — table view replaces mobile cards
1024px — desktop: sidebar pinned, topbar static
1440px — extended spacing, 4-col stats
```

## Component Design

### Sidebar (off-canvas drawer mobile / fixed desktop)
- Background: wine-800 to wine-950 gradient with subtle gold radial highlight
- Brand mark: circular gold container with ॐ
- Nav links: 48px min-height, 13px font, gap 12px
- Active state: gold gradient background, dark text, left accent bar
- Badge: pill for pending counts
- Profile section: avatar + name/role in subtle highlight area
- Logout link: muted red tone, separated at bottom

### Topbar
- Sticky, white/cream with backdrop blur
- Height: 56px mobile, 64px desktop
- Elements: hamburger (44×44px touch target), title (17px mobile, 19px+ desktop), breadcrumb (desktop/tablet), View Site button, profile area (desktop)
- Minimal, does not wrap on mobile

### Dashboard Stats Cards
- White card, 1px line border, 14px radius, shadow
- Icon in cream circle (36×36px), label below
- Value: 25px–32px, bold, wine color
- Label: 11px muted, optional Nepali subtitle
- Hover: slight lift with stronger shadow
- Trend indicator (optional, green percentage)

### Data Tables (desktop/tablet)
- Full-width, collapsed borders, 12px font
- Sticky header: wine background, cream uppercase text
- Row hover: subtle cream
- Thumbnail images: 36×36px rounded
- Status badges, amount columns right-aligned
- Actions column: three-dot menu or inline icon buttons
- Wrapped in overflow-x: auto container

### Mobile Record Cards (<820px)
- Each table row becomes a card
- Primary name, status badge, key fields in 2-column grid
- Action button (three-dot) or inline links
- Expandable details for additional fields
- Cards stack vertically with 10px gap

### Forms
- Full-width inputs, 44px height, 10px radius
- Labels: 11px uppercase font-weight 600, above inputs
- Validation: red border + error message below field
- 2-column grid on ≥640px, single-column below
- Sticky bottom action bar on long mobile forms
- File upload: dashed drop zone, preview thumbnail, clear button
- Password toggle: eye icon inside input wrapper

### Badge System
```
.pending    → amber bg, amber text + dot
.confirmed  → blue bg, blue text + dot  
.completed  → green bg, green text + dot
.cancelled  → red bg, red text + dot
.draft      → gray bg, gray text
.published  → green bg, green text
.active     → green bg, green text
.inactive   → gray bg, gray text
.processing → indigo bg, indigo text
```

Each badge has `::before` dot (6px) for non-color status communication.

### States
- **Loading:** Skeleton placeholder with shimmer animation or centered spinner
- **Empty:** Centered icon + title "No records yet" + explanation text + optional CTA button
- **Error:** Red error icon + failure message + Retry button
- **Success alert:** Green banner at top, auto-dismiss optional

## Page-by-Page Impact

### Login page (`admin/index.php`)
- Centered card layout (400px max-width), warm gradient background
- Brand logo (ॐ in gold circle), title, subtitle
- Form fields with visible labels, password toggle
- Remember-me checkbox, full-width primary button
- "Back to website" link below
- Error alert inline above form
- Layout already reasonable; minor polish, enhance responsive padding

### Dashboard (`admin/dashboard.php`)
- Replace inline `style` blocks with CSS classes
- Stats grid using reusable `.stat-card` component
- Recent appointments section: list-item pattern with avatar, name, service, date, badge, action
- Recent messages section: same pattern, unread indicator (bold + amber dot)
- Bottom 4 summary stats: 4-col grid desktop, 2-col tablet, 1-col mobile
- Remove hard-coded inline `style="display:grid;grid-template-columns:1fr 1fr;..."`
- Both Nepali labels kept but formatted with proper component CSS

### Appointments (`admin/appointments.php`)
- Page header with title + optional "Add" button
- Filter tabs as horizontal pill bar (pending/confirmed/completed/cancelled/all)
- Table converted to class-based `.data-table` with responsive card fallback
- Detail toggle row: expandable detail section with form controls
- Inline status update: select + submit button with CSRF
- Detail grid: 2-col on tablet+, single on mobile
- Admin notes textarea

### Pooja Orders (`admin/pooja-orders.php`)  
- Same pattern as appointments
- Filter tabs, data-table, inline status update form
- Details: service name, live stream indicator, preferred date/time
- Admin notes inline

### Payments (`admin/payments.php`)
- Filter tabs (pending/approved/rejected/all)
- Data table with screenshot link, amount, method, reference
- Action buttons: Approve (green) / Reject (red) with notes field
- Confirmation before action
- Table > mobile cards pattern

### Products (`admin/products.php`)  
- Page header + action button
- Form card + table split layout (2-col grid desktop, stacked mobile)
- Product edit/create form with `form-grid`
- Product table: name (bilingual), price, category, stock badge, status badge, action buttons
- Toggle active, edit, delete actions with CSRF

### Pooja Services (`admin/pooja-services.php`)
- Same layout as products: form + table
- Category select, price, duration, materials checkbox
- Status toggle, edit, delete

### Articles (`admin/articles.php`)
- Form card + table split
- Rich form: bilingual titles, slug (auto-generated), excerpt, content textareas, cover image URL
- Table: title (bilingual), slug, published badge, date, action buttons (view/edit/delete)
- Confirmation on delete

### Panchang (`admin/panchang.php`)
- Date picker with auto-calculated values display
- Form card with all panchang fields
- Recent entries table
- Responsive 2-col split

### Messages (`admin/messages.php`)
- Filter tabs (all/unread)
- Data table with detail toggle row
- Mark as read, delete actions
- Unread row highlighting

### Settings (`admin/settings.php`)
- Centered form card (max-width 500px)
- Password change form with current, new, confirm fields
- Simple and clean

## Accessibility Checklist
- All icon buttons have `aria-label`
- Sidebar has `aria-label="Main navigation"`, `role="navigation"`
- Menu toggle uses `aria-expanded` and `aria-controls`
- Focus trap in mobile drawer
- Focus returns to toggle on close
- `:focus-visible` outlines on all interactive elements (3px gold)
- `prefers-reduced-motion` reduces all transitions/animations
- Status communicated with text + color (badge dot `::before`)
- Form labels linked to inputs via `for`/`id`
- Touch targets ≥44×44px
- Escape key closes drawer, modals
- Logical heading hierarchy (`h1` > `h2` > `h3`)

## Responsive Approach
- CSS `min-width: 0` on all flex/grid children with text overflow
- No hard-coded widths; use `min()`, `max()`, `clamp()` where useful
- Tables: `overflow-x: auto` on wrapper; mobile cards via media query
- Images: `max-width: 100%`, `object-fit` as needed
- Sidebar: `transform: translateX(-102%)` for mobile; `transform: none` on desktop
- Safe areas via `env(safe-area-inset-*)`

## Implementation Order
1. CSS file: consolidate all styles into `assets/admin.css` (the single stylesheet linked by `header.php`)
2. Login page: refine existing layout with new design tokens
3. Sidebar + header: refactor `header.php` layout
4. Footer: refactor `footer.php` with mobile drawer JS
5. Dashboard: convert to new card system
6. Appointments, Pooja Orders, Payments: table → data-table + mobile cards
7. Products, Pooja Services, Articles: form + table split pages
8. Panchang, Messages, Settings: remaining pages
9. Accessibility audit and final polish

## Constraints
- Do NOT modify any PHP logic, SQL queries, form POST handlers, CSRF functions, or authentication
- Do NOT remove or change `name` attributes on form inputs
- Do NOT change `action` attributes on forms
- Do NOT remove `<?= csrfField() ?>` or `<?= csrfQuery() ?>`
- All existing `id` attributes used by PHP or JS must be preserved
- The `header.php` and `footer.php` includes must remain in place
- No frontend framework bundler — pure CSS + vanilla JS only
