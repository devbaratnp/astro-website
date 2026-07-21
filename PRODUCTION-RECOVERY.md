# Production Recovery — Subdirectory Deployment Fix

## Problem

The site at `http://localhost/www/astrohari/` was serving 404 errors because all asset paths, API calls, and SPA routes were hardcoded as root-absolute (`/...`) but Apache serves the site from the `/www/astrohari/` subdirectory.

## Root Cause

The Vite SPA was built with `base: "/"` (default), so the built `index.html` referenced assets like `/assets/index-*.js`. The PHP backend API was hardcoded as `/backend/api/...` in the source code. Both paths resolve to the server root, not the subdirectory where the site lives.

## Changes Made

### 1. Vite Config — `astro-shree-hari-source/vite.config.mjs`

Added `base: "/www/astrohari/"` so all built asset references (JS, CSS) use the correct subdirectory prefix.

### 2. Source API Paths — 7 files fixed

| File | Change |
|------|--------|
| `src/services/api.js` | `API_BASE` from `/backend/api` → `/www/astrohari/backend/api` |
| `src/pages/Admin.jsx` | `API` constant + admin nav links pointing to `/` → `/www/astrohari/` |
| `src/components/ErrorBoundary.jsx` | `fetch("/backend/api/..."` → `/www/astrohari/backend/api/...` |
| `src/pages/Article.jsx` | `fetch("/backend/api/articles.php?slug=..."` → prefixed |
| `src/pages/Blog.jsx` | `fetch("/backend/api/articles.php"` → prefixed |
| `src/pages/Events.jsx` | Two `fetch("/backend/api/events.php?...")` → prefixed |
| `src/pages/Gallery.jsx` | `fetch("/backend/api/gallery.php..."` → prefixed |

### 3. Source `index.html` — favicon/apple-touch-icon

Changed `/assets/shreehari-icon-192.png` → `/www/astrohari/assets/shreehari-icon-192.png`

### 4. `.htaccess` — `D:\www\astrohari\.htaccess`

- `RewriteBase /` → `RewriteBase /www/astrohari/`
- All redirect URLs updated to include `/www/astrohari/` prefix

### 5. Service Worker Removed

- `sw.js` deleted from web root
- SW registration call removed from source/JS bundle

### 6. `manifest.json`

- `start_url`, icon paths updated to `/www/astrohari/...`

### 7. Rebuild & Deploy

- `npm run build` (Vite) → outputs to `dist/`
- Copied `dist/index.html`, `dist/assets/*` to `D:\www\astrohari/`
- Removed stale old JS bundle (`index-B9a7v_jO.js`)

## Files Preserved (not touched by build)

- `backend/` — PHP API (unchanged)
- `.htaccess` — Apache config (manually fixed)
- `manifest.json` — PWA manifest (manually fixed)
- `assets/*.png`, `assets/*.webp`, `assets/*.jpeg` — static images
- `assets/admin.css` — admin styles

## Verification

- All `/backend/api` in built JS are prefixed with `/www/astrohari/backend/api` (3 occurrences, all correct)
- No bare `/backend/api` references remain
- Favicon/apple-touch-icon paths use correct prefix
- Old JS bundle removed, new bundle deployed

## To Test

Open `http://localhost/www/astrohari/` in browser. Check:
- Page loads without 404s
- Blog, Events, Gallery pages load API data
- Admin panel at `/www/astrohari/admin` works
- Static assets (images, CSS) load
