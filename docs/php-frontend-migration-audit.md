# PHP Frontend Migration Audit

## Current Architecture

```
D:\www\astrohari/
├── index.html                    # React SPA entry (built bundle)
├── assets/
│   ├── index-DAlHnzkw.js        # React JS bundle (Vite built)
│   ├── index-hmChPcuo.css       # Full CSS bundle (Vite built)
│   ├── js/
│   │   └── site.js              # Vanilla JS: mobile menu, clock tick
│   ├── css/
│   │   ├── site.css             # Core responsive CSS (maroon/gold/ivory)
│   │   └── pages/
│   │       ├── home.css         # Hero, trust bar, astro hub cards
│   │       ├── about.css        # About section, biography, credentials
│   │       ├── forms.css        # Booking/contact/payment/kundali forms
│   │       ├── panchang.css     # Panchang nav, tabs, horoscope grid
│   │       ├── blog.css         # Blog grid, article layout
│   │       └── gallery.css      # Events, gallery, store CSS
│   ├── shreehari-logo.webp
│   ├── shreehari-icon-192.png
│   └── sitaram-timilsena.jpeg
├── includes/
│   ├── public-config.php        # DB helper, services list, constants
│   ├── public-seo.php           # renderSeo() with schema.org JSON-LD
│   ├── public-header.php        # renderPublicHeader() — full nav
│   ├── public-footer.php        # renderPublicFooter() — full footer + whatsapp
│   └── public-icons.php         # renderIcon() — 40+ SVG Phosphor icons
├── astro-shree-hari-source/     # React source (to be archived)
│   └── src/
│       ├── App.jsx              # Router: 17 routes
│       ├── main.jsx             # React entry
│       ├── config.js            # API_BASE = /backend/api
│       ├── constants.js         # PHONE, EMAIL, services array
│       ├── components/
│       │   ├── Layout.jsx       # Header + nav + footer + floating whatsapp
│       │   └── ErrorBoundary.jsx
│       ├── pages/
│       │   ├── Home.jsx         # Hero, trust bar, panchang, services, about, blog, testimonials
│       │   ├── About.jsx        # Bio, education, contribution, awards, contact
│       │   ├── Services.jsx     # Service cards from constants
│       │   ├── Appointment.jsx  # BS date picker, birth details, slot booking
│       │   ├── Contact.jsx      # Contact cards, map, message form
│       │   ├── Kundali.jsx      # Birth details form → API → rashi/nakshatra/lagna
│       │   ├── Pooja.jsx        # Service listing, booking form
│       │   ├── Panchang.jsx     # Date nav, tithi/nakshatra, day/night tabs, horoscope
│       │   ├── Payment.jsx      # QR placeholder, payment form with upload
│       │   ├── Blog.jsx         # Article grid from API
│       │   ├── Article.jsx      # Single article with SEO/metadata
│       │   ├── Events.jsx       # Events/tours tabs
│       │   ├── Gallery.jsx      # All/video/image tabs, lightbox
│       │   ├── Muhurta.jsx      # BS date picker, nakshatra check, verdict
│       │   └── Store.jsx        # Product cards by category
│       └── utils/
│           ├── bsDate.js        # BS-AD date conversion (1970-2090)
│           ├── homeAstrology.js # Panchang field display helpers
│           ├── panchangDisplay.js # Row/forecast builders
│           └── panchangEngine.js # Surya sidhanta panchang calculation
├── backend/
│   ├── api/                     # All PHP API endpoints
│   ├── config/                   # DB, app, CORS, credentials
│   ├── includes/                # helpers.php, error-handler.php
│   ├── lib/                     # Panchang, Astrology, KundaliInquirySaver, Mailer, GoogleCalendar
│   ├── middleware/              # validate.php
│   └── tests/                   # KundaliFlowTest, KundaliInquirySaverTest
├── admin/                       # PHP admin panel (separate from public)
└── .htaccess                    # Rewrites to React SPA
```

## Public Route Inventory

| Route | React Page | PHP File Needed | Interactive? | API Endpoint(s) |
|-------|-----------|----------------|--------------|-----------------|
| `/` | Home.jsx | index.php | Panchang (JS fetch) | panchang.php, testimonials.php, articles.php |
| `/about` | About.jsx | about.php | No | None |
| `/services` | Services.jsx | services.php | No | None |
| `/appointment` | Appointment.jsx | appointment.php | Yes (lots) | appointments.php |
| `/contact` | Contact.jsx | contact.php | Contact form | contact.php |
| `/kundali` | Kundali.jsx | kundali.php | Yes | kundali.php |
| `/pooja` | Pooja.jsx | pooja.php | Yes | pooja.php |
| `/panchang` | Panchang.jsx | panchang.php | Yes | panchang.php, horoscope.php |
| `/payment` | Payment.jsx | payment.php | Yes | payments.php |
| `/blog` | Blog.jsx | blog.php | No | articles.php |
| `/article/{slug}` | Article.jsx | article.php | No | articles.php |
| `/events` | Events.jsx | events.php | Tab switch | events.php |
| `/gallery` | Gallery.jsx | gallery.php | Filter, lightbox | gallery.php |
| `/muhurta` | Muhurta.jsx | muhurta.php | Yes | (client-side calc) |
| `/store` | Store.jsx | store.php | Order toggle | products.php |

## API Inventory

| Endpoint | Method | Purpose | React Consumer |
|----------|--------|---------|---------------|
| `panchang.php?date=` | GET | Panchang data | Home, Panchang |
| `horoscope.php?date=` | GET | Daily horoscope | Panchang |
| `kundali.php` | POST | Basic kundali | Kundali |
| `appointments.php` | GET/POST | Slots + booking | Appointment |
| `contact.php` | POST | Contact form | Contact |
| `pooja.php` | GET/POST | Services list + booking | Pooja |
| `articles.php` | GET | Article list + single | Blog, Article |
| `events.php` | GET | Events/tours | Events |
| `gallery.php` | GET | Media items | Gallery |
| `products.php` | GET | Store products | Store |
| `payments.php` | POST | Submit payment | Payment |
| `testimonials.php` | GET | Testimonials | Home |

## Database-Dependent Features

- Appointments CRUD via `appointments` table
- Contact messages via `contact_messages` table
- Articles via `articles` table
- Events/tours via `events` table
- Gallery via `gallery` table
- Products via `products` table
- Panchang caching via `panchang` table
- Pooja services via `pooja_services` table
- Kundali inquiries via `appointments` table (service_type='kundali')

## JavaScript-Dependent Features (must convert to vanilla JS)

1. **Mobile menu toggle** — already in site.js ✓
2. **Home clock ticker** — already in site.js ✓
3. **Home panchang fetch** — needs page-specific JS
4. **Appointment form** — BS date picker, AM/PM toggle, slot loading, form submission, WhatsApp redirect
5. **Kundali form** — form submission, result display
6. **Panchang page** — date nav, tabs, data fetch
7. **Muhurta page** — BS date picker, nakshatra calculation
8. **Pooja booking** — service selection, form, material download
9. **Payment form** — file to base64, form submission
10. **Gallery lightbox** — click to open/close
11. **Events tab switching** — upcoming/tour toggle

## Existing PHP Pages That Can Be Reused

- `includes/public-*.php` — all 5 files are ready
- `assets/js/site.js` — mobile menu + clock ✓
- `assets/css/*.css` — all styles ready ✓
- `backend/api/*.php` — all APIs ready ✓

## Dead or Duplicated Code

- `admin-redesign-preview.html` — seems like a preview, possibly obsolete
- `panchang-reference-preview.html` — reference preview
- `preview-single-file.html` — preview file
- `__test_panchang.php` — test file
- `astro-shree-hari-source/` — entire React source (to archive after migration)
- `assets/index-DAlHnzkw.js` — React bundle (to remove after migration)
- `assets/index-hmChPcuo.css` — Vite CSS bundle (to remove after migration, already split)

## Known React Errors (observed in code review)

1. **Kundali API response shape** — `getKundali()` in `api.js` returns `data.data` (the `request()` function unwraps to `data`). In `Kundali.jsx:setResult(x.kundali)`, this should work if API returns `{kundali: ..., message: ...}` wrapped in `data`. The API returns `{kundali: ..., message: ...}` wrapped in `jsonSuccess()` as `{success, message, data: {kundali, message}}`. The `request()` function returns `data.data` = `{kundali, message}`. So `x.kundali` should work. **FIXED in current code.**

2. **Payment API response** — `Payment.jsx` accesses `x.id || x.data?.id`. The `request()` function unwraps `data.data`, so `x.id` should be correct. But the fallback `x.data?.id` suggests uncertainty about the API contract.

## Known API Response-Contract Problems

The shared `request()` function in `api.js`:
```js
async function request(endpoint, options = {}) {
  const url = `${API_BASE}/${endpoint}`;
  const res = await fetch(url, { ... });
  const data = await res.json().catch(() => ...);
  if (!data.success) throw new Error(data.message);
  return data.data;  // Returns inner data object
}
```

The PHP `jsonSuccess()`:
```php
function jsonSuccess(mixed $data, string $message = 'OK'): void {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}
```

So the chain is:
- PHP returns `{success: true, message: "OK", data: {kundali: {...}}}`
- `request()` returns `data.data` = `{kundali: {...}}`
- React uses `x.kundali` → correct

**This is consistent. No bug here.**

## Current Rewrite Rules (.htaccess)

```apache
RewriteRule ^index\.php$ / [R=301,L,NE]
RewriteRule ^about\.php$ /about [R=301,L,NE]
RewriteRule ^services\.php$ /services [R=301,L,NE]
RewriteRule ^appointment\.php$ /appointment [R=301,L,NE]
RewriteRule ^contact\.php$ /contact [R=301,L,NE]
RewriteRule ^$ index.html [L]
RewriteRule ^admin/?$ admin/index.php [L]
RewriteRule ^webhook\.php$ - [L]
RewriteRule ^backend/ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]
```

All unknown routes go to `index.html` (React SPA). Needs replacement with explicit PHP routes.

## Migration Risks

1. **Visual parity** — CSS is already split, but some React-specific styles from the Vite bundle may be missing
2. **BS Date conversion** — Appointment and Muhurta pages use `bsDate.js` utilities that must be ported
3. **Panchang calculation** — `panchangEngine.js` uses Surya Siddhanta algorithm; the PHP `Panchang.php` is a simplified approximation
4. **Kundali calculation** — React version depends on PHP backend `Astrology.php` which is already done
5. **SEO metadata** — React `Layout.jsx` dynamically sets meta tags per page; PHP `renderSeo()` already handles this
6. **Article structured data** — React `Article.jsx` dynamically injects JSON-LD; PHP needs equivalent
7. **Form file uploads** — Payment form uses `FileReader.readAsDataURL` for base64; PHP backend expects this
8. **Testimonials require DB** — If DB unavailable, home page should gracefully degrade

## Recommended Implementation Order

1. ✅ Phase 2: Fix Kundali 500 error (already done)
2. ✅ Phase 3: Build reusable PHP public frontend system (already done)
3. **Phase 4-6: Convert React pages to PHP** (current task)
   - Start with static pages: index, about, services, blog, contact
   - Then interactive pages: panchang, kundali, appointment, muhurta
   - Then special pages: article, events, gallery, pooja, payment, store
4. Phase 7: Update .htaccess routing
5. Phase 8: Security review
6. Phase 9-10: Backend preservation & remove React

## CSS File Mapping

| React CSS Import | PHP CSS File |
|-----------------|-------------|
| `styles.css` - from App.jsx | `assets/css/site.css` |
| `homeAstrology.css` - from Home.jsx | `assets/css/pages/home.css` |
| `admin.css` - admin route | (PHP admin, separate) |
| (article layout in blog.css) | `assets/css/pages/blog.css` |
| (form styles) | `assets/css/pages/forms.css` |
| (panchang styles) | `assets/css/pages/panchang.css` |
| (gallery/events/store) | `assets/css/pages/gallery.css` |

## JS File Plan

| Page | JS File | Features |
|------|---------|----------|
| All | `assets/js/site.js` | Mobile menu, clock ✓ |
| Home | `assets/js/home.js` | Panchang fetch, testimonials |
| Panchang | `assets/js/panchang.js` | Date nav, tabs, data fetch |
| Kundali | `assets/js/kundali.js` | Form submit, result display |
| Appointment | `assets/js/appointment.js` | BS date picker, slot booking |
| Muhurta | `assets/js/muhurta.js` | BS date picker, calculation |
| Pooja | `assets/js/pooja.js` | Service selection, booking |
| Payment | `assets/js/payment.js` | File upload, form submit |
| Gallery | `assets/js/gallery.js` | Tab filter, lightbox |
| Events | `assets/js/events.js` | Tab switching |
