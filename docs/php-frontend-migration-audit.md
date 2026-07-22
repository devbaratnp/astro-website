# Astro Shree Hari — Public Frontend PHP Migration Audit

**Date:** 2026-07-22  
**Target Repository:** `devbaratnp/astro-website`  
**Production Domain:** `https://www.astroshreehari.com`  

---

## 1. Current Architecture Overview

The current system is composed of:
1. **Public Frontend (Vite + React SPA):**
   - Source located in `astro-shree-hari-source/`.
   - Built assets deployed to root `index.html` and `assets/index-*.js`, `assets/index-*.css`.
   - Uses `react-router-dom` for client-side routing across 15 public routes.
   - Relying on `@phosphor-icons/react` for iconography.
2. **Backend Services & API:**
   - Native PHP files in `backend/api/` providing JSON REST-ish endpoints.
   - PDO database abstraction in `backend/config/database.php` reading from environment variables or `database.credentials.php`.
   - Helper functions in `backend/includes/helpers.php` and error/exception handlers in `backend/includes/error-handler.php`.
3. **Admin Portal:**
   - Native PHP server-side admin panel located in `admin/` (`admin/index.php`, `admin/dashboard.php`, etc.).
   - Communicates with `backend/api/admin.php` and directly queries MySQL database.
4. **Database Schema:**
   - MySQL database containing tables for `appointments`, `articles`, `events`, `gallery`, `horoscope`, `messages`, `panchang`, `payments`, `pooja_bookings`, `pooja_services`, `products`, `settings`, `testimonials`, and `users`.
5. **Server Routing & Security (.htaccess):**
   - Enforces HTTPS and canonical `www` domain.
   - Intercepts root `.php` requests and redirects to React SPA routes.
   - Maps non-file/directory requests to `index.html` (SPA fallback).

---

## 2. Public Route Inventory

| React Route | File Path (`src/pages/`) | Target PHP Page | Primary Functionality & Features |
| :--- | :--- | :--- | :--- |
| `/` | `Home.jsx` | `index.php` | Hero banner, Astrologer portrait, Trust bar, Live clock, Daily Panchang summary, Tools grid, Service cards, About summary, Process steps, Articles, Testimonials. |
| `/about` | `About.jsx` | `about.php` | Biography of Pt. Sitaram Timalsena, Gurukul & university education, contributions (139 Mahapuranas), credentials/memberships, awards, official addresses (Nepal & USA), English bio. |
| `/services` | `Services.jsx` | `services.php` | Grid of 6 core astrological services (Kundali, Marriage, Vastu, Graha Shanti, E-Pooja, Muhurta). |
| `/appointment` | `Appointment.jsx` | `appointment.php` | Interactive appointment form, BS date picker (Year, Month, Day), AM/PM time selector, birth details, slot picker fetching from `appointments.php`, WhatsApp redirection & video consultation link. |
| `/contact` | `Contact.jsx` | `contact.php` | Contact details, office locations, social links, embedded Google Map iframe, message form submitting to `contact.php`. |
| `/kundali` | `Kundali.jsx` | `kundali.php` | Form for birth date/time/place, API submit to `kundali.php`, rendering of Rashi, Nakshatra, Lagna, place, and 12-house Lagna grid. |
| `/pooja` | `Pooja.jsx` | `pooja.php` | E-Pooja service listing from `pooja.php`, material list text file download, booking form submitting to `pooja.php`. |
| `/panchang` | `Panchang.jsx` | `panchang.php` | Daily Panchang details for selected date, date navigation (+/- 1 day & date picker), Day/Night horoscope tabs, 12 Rashi forecast cards. |
| `/payment` | `Payment.jsx` | `payment.php` | Static QR payment guidelines, payment verification form (booking type/ID, amount, transaction ref, screenshot file upload) submitting to `payments.php`. |
| `/blog` | `Blog.jsx` | `blog.php` | Blog article listing from `articles.php`, cover images, excerpts, publish dates. |
| `/article/:slug` | `Article.jsx` | `article.php` | Article detail page fetched from `articles.php?slug={slug}`, dynamic SEO tags, JSON-LD Schema, rendered HTML content, back link, share button. |
| `/events` | `Events.jsx` | `events.php` | Discourses & tours listing from `events.php?type=...`, filter tabs (Events vs Tours), event cards, registration button. |
| `/gallery` | `Gallery.jsx` | `gallery.php` | Media gallery from `gallery.php?type=...`, tabs (All, Video, Photo), lightbox modal for photos, external video player link. |
| `/muhurta` | `Muhurta.jsx` | `muhurta.php` | Muhurta calculation form (Marriage, Grihapravesh, Bartabandha, Business, Travel), Bikram Sambat date selector, verdict output (Shubha/Madhyam/Ashubha) with details. |
| `/store` | `Store.jsx` | `store.php` | Pooja Store product catalog from `products.php` grouped by category, stock status badges, prices, order overlay with contact channels. |

---

## 3. API Inventory & Backend Endpoints

All endpoints reside in `backend/api/`:
- `appointments.php`: `GET` (slots for a specific date), `POST` (create appointment record & return meeting room URL).
- `articles.php`: `GET` (list published articles or fetch single article by `slug`).
- `contact.php`: `POST` (save contact message to `messages` table).
- `events.php`: `GET` (list events filtered by `type=event` or `type=tour`).
- `gallery.php`: `GET` (list gallery items filtered by `type=image` or `type=video`).
- `horoscope.php`: `GET` (list daily horoscope forecasts for a given date).
- `kundali.php`: `POST` (calculate basic Kundali details & save inquiry record).
- `muhurta.php`: `GET` (muhurta backend calculations).
- `panchang.php`: `GET` (daily panchang facts for a given date).
- `payments.php`: `POST` (save static QR payment verification request & screenshot).
- `pooja.php`: `GET` (list pooja services), `POST` (create pooja booking record).
- `products.php`: `GET` (list active store products).
- `testimonials.php`: `GET` (list published testimonials).
- `admin.php`, `auth.php`, `upload.php`, `log-frontend-error.php`: Management, authentication, upload, and error logging endpoints.

---

## 4. Database-Dependent Features

The MySQL database contains tables required for:
1. **Appointments (`appointments`)**: Customer details, service requested, birth date/time/place, status, meeting URL.
2. **Articles (`articles`)**: News/articles content in Nepali, slug, cover image, publish status.
3. **Events (`events`)**: Events and tour schedules, location, contact info, registration links.
4. **Gallery (`gallery`)**: Photos and video links with titles and categories.
5. **Horoscope & Panchang (`horoscope`, `panchang`)**: Daily astrological data, Tithi, Nakshatra, Sunrise, Sunset, Zodiac forecasts.
6. **Messages (`messages`)**: Contact form inquiries.
7. **Payments (`payments`)**: Static QR payment submissions, transaction reference, uploaded screenshot filenames.
8. **Pooja Services & Bookings (`pooja_services`, `pooja_bookings`)**: E-Pooja items, pricing, custom options, booking requests.
9. **Products (`products`)**: Store items, price, compare price, category, stock status.
10. **Testimonials (`testimonials`)**: Client feedback and locations.
11. **Settings & Users (`settings`, `users`)**: Admin account credentials, site configuration parameters.

---

## 5. JavaScript-Dependent Features (To be built with Vanilla JS)

- **Global Navigation:** Mobile hamburger toggle menu state.
- **Home Page:** Real-time ticking Nepal clock (`Asia/Kathmandu`).
- **Appointment Page:**
  - Bikram Sambat date selector calculation & AD conversion.
  - Dynamic slot loading via `fetch('/backend/api/appointments.php?date=...')`.
  - Form validation & WhatsApp link opening.
- **Kundali Page:**
  - AJAX submission to `/backend/api/kundali.php` & rendering calculation results inline.
- **Panchang & Horoscope Page:**
  - Date navigation buttons (+/- 1 day) updating URL query parameter `?date=YYYY-MM-DD`.
  - Day vs Night forecast tab switching.
- **Muhurta Page:**
  - Bikram Sambat date selector.
  - Client-side or API-driven Muhurta verdict calculation.
- **E-Pooja Page:**
  - Material list `.txt` client-side download generation.
  - Service selection modal / booking overlay.
- **Gallery Page:**
  - Category tab filtering (All / Photos / Videos).
  - Lightbox image viewer modal.
- **Payment Page:**
  - Form validation & file upload handling.

---

## 6. Dead Code & Abandoned Files Audit

The audit identified the following non-production or redundant files:
- **`preview-single-file.html`**: Old static mockup (35 KB).
- **`admin-redesign-preview.html`**: Old admin static preview (31 KB).
- **`panchang-reference-preview.html`**: Reference preview (3.3 KB).
- **`__test_panchang.php`**: Scratch test file in root directory.
- **`assets/index-9kgXQgUr.js`, `assets/index-DAlHnzkw.js`, `assets/index-HXUnpGQq.css`, `assets/index-hmChPcuo.css`**: Legacy Vite React output bundles.

---

## 7. Known Production Errors & API Contract Bugs

### 7.1 Kundali Endpoint HTTP 500 Failure (`backend/api/kundali.php`)
- **Root Cause:** Line 46 calls `KundaliInquirySaver::save(Database::getConnection(), $input)` directly. `Database::getConnection()` is evaluated before entering `KundaliInquirySaver::save()`'s internal `try/catch`. When the MySQL database server is unreachable or credentials fail, `Database::getConnection()` throws a `PDOException` / `RuntimeException` that escapes uncaught, resulting in a server 500 error, even though the basic astronomical Kundali calculation succeeded!
- **Missing Validation:** `kundali.php` line 16 only validates `name`. Missing fields or invalid date formats like `2026-99-99` crash `DateTime` construction.
- **Corrupted UTF-8 Text:** `KundaliInquirySaver.php` contains corrupted byte characters: `':message' => '???????? ??????? ??????? ??????? ??????'` instead of proper Nepali text `'स्वचालित कुण्डली हेरेपछि परामर्श अनुरोध'`.

### 7.2 Frontend API Response Unwrapping Mismatch
- The shared frontend client helper `request()` unwraps response JSON and returns `data.data`.
- In `Kundali.jsx`: `const x = await getKundali(...); setResult(x.data.kundali);` causes a runtime `TypeError` because `x` is already `data.data` (`x.data` is `undefined`).
- In `Payment.jsx`: `const x = await submitPayment(...); setMsg('... ' + x.data.id);` causes a similar runtime error because `x` is already `data.data`.

---

## 8. Current Rewrite Rules (.htaccess)

Currently, `.htaccess` redirects old `.php` URLs (`index.php`, `about.php`, `services.php`, `appointment.php`, `contact.php`) to SPA routes, and falls back to `index.html` for all unknown routes.

```apache
RewriteEngine On
RewriteBase /
DirectoryIndex index.html

# Canonical redirect
RewriteCond %{HTTP_HOST} !^localhost(:[0-9]+)?$ [NC]
RewriteCond %{HTTP_HOST} !^127\.0\.0\.1(:[0-9]+)?$ [NC]
RewriteCond %{HTTPS} off [OR]
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^ https://www.astroshreehari.com%{REQUEST_URI} [L,R=301]

# React SPA Fallback
RewriteRule ^$ index.html [L]
RewriteRule ^admin/?$ admin/index.php [L]
RewriteRule ^webhook\.php$ - [L]
RewriteRule ^backend/ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]
```

**Target Rewrite Strategy:**
Replace `index.html` fallback with clean PHP route rules (e.g. `RewriteRule ^about/?$ about.php [L,QSA]`).

---

## 9. Migration Risks & Mitigation Plan

1. **Risk:** Disruption to backend admin & APIs during rewrite rule changes.  
   *Mitigation:* Explicitly preserve `/admin/` and `/backend/` rules prior to any public route rewrites.
2. **Risk:** SEO penalty or broken social share preview during migration.  
   *Mitigation:* Implement full server-rendered SEO meta tags (`public-seo.php`), Open Graph, and JSON-LD structured data directly in PHP pages.
3. **Risk:** UTF-8 text encoding issues in Nepali strings.  
   *Mitigation:* Save all `.php` files in UTF-8 without BOM and set `header('Content-Type: text/html; charset=utf-8');`.

---

## 10. Recommended Implementation Order

1. **Phase 2:** Refactor `backend/api/kundali.php` & `KundaliInquirySaver.php`, validate input fields, fix response contract, and add PHP unit tests for database failure isolation.
2. **Phase 3:** Create shared PHP frontend components (`includes/public-config.php`, `includes/public-seo.php`, `includes/public-header.php`, `includes/public-footer.php`, `includes/public-icons.php`).
3. **Phase 4:** Consolidate CSS into modular assets (`assets/css/site.css`, `assets/css/pages/*.css`).
4. **Phase 5 & 6:** Build 15 server-rendered `.php` pages with vanilla JavaScript helpers (`assets/js/site.js`, `assets/js/appointment.js`, `assets/js/kundali.js`, `assets/js/panchang.js`, etc.).
5. **Phase 7:** Update `.htaccess` with clean PHP rewrite rules and custom 404 page.
6. **Phase 8 & 9:** Security audit (CSRF tokens, input sanitization, upload checks) and backend regression testing.
7. **Phase 10:** Retire React SPA assets, write parity report, and finalize documentation.
