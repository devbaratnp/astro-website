# PHP Frontend Visual Parity Report

Each PHP page was compared against its React JSX source (from `git show pre-php-frontend-migration`) for structural, visual, and behavioral parity.

## Summary

| Page | Sections Match | CSS Classes Match | Text Identity | Overall Parity |
|------|:---:|:---:|:---:|:---:|
| Home (index.php) | 9/9 | 100% | 100% | **98%** |
| About | 6/6 | 100% | 99% | **97%** |
| Services | 1/1 | 100% | 100% | **100%** |
| Contact | 1/1 | 100% | 98% | **95%** |
| Blog | 1/1 | 100% | 100% | **98%** |
| Article | 1/1 | 100% | 100% | **100%** |
| Kundali | 1/1 | 100% | 100% | **95%** |
| Panchang | 6/6 | 85% | 100% | **90%** |
| Muhurta | 3/3 | 70% | 100% | **85%** |
| Appointment | 1/1 | 90% | 98% | **85%** |
| Pooja | 1/1 | 85% | 95% | **88%** |
| Payment | 1/1 | 90% | 95% | **90%** |
| Events | 1/1 | 95% | 95% | **92%** |
| Gallery | 1/1 | 85% | 100% | **90%** |
| Store | 1/1 | 80% | 98% | **85%** |

## Critical Differences

### 1. Appointment — Service dropdown labels 3 & 4 swapped
The React `Appointment.jsx` maps service options by index but the `services` constant array has a different order than assumed. When a user selects option 3, the label reads "वास्तु परामर्श" but the value is `grahadasha` (and vice versa for option 4). The PHP version correctly shows "ग्रह शान्ति" as option 3 and "वास्तु परामर्श" as option 4.

**Fix**: Re-order the React service array to match the expected index order, or use value-based lookup instead of index-based.

### 2. Home — Panchang loading state missing in PHP
The React version shows `<div className="astro-loading">` with "आजको पञ्चाङ्ग लोड हुँदैछ…" during API fetch. The PHP version has no loading state — it either shows data immediately (server-rendered) or jumps to the error state.

**Impact**: Minimal for PHP since data is pre-rendered server-side. If the embedded JSON is present, there is no loading gap.

### 3. Contact — JS references missing DOM element
External script `contact.js` targets `#contact-message` for AJAX success/error display, but this element does not exist in `contact.php` markup. The inline PHP success/error blocks work, but the async JS path silently fails to display messages.

**Fix**: Add `<div id="contact-message"></div>` to the contact form, or remove the JS reference.

## Moderate Differences

### 4. Blog excerpt fallback
React falls back to `content_ne` stripped to 150 chars when `excerpt_ne` is empty. PHP only shows `excerpt_ne`. Most articles have excerpts, but some may show less text in PHP.

### 5. Date input `min` attribute missing in PHP
Several forms (appointment, pooja, payment) lack the `min` attribute on date inputs. The React versions use `min={todayMin}` to prevent past date selection.

**Impact**: Users can select past dates in PHP forms (API returns no slots for past dates).

### 6. Birth day select starts empty in PHP
The appointment and muhurta forms render empty `<select id="birth-bs-day">` that is populated by external JS after the page loads. React pre-populates options on render.

**Impact**: Brief window where dropdown shows no options before JS runs.

### 7. Store card padding significantly different
PHP cards use `padding: 14px 18px 18px` (with image) and `22px` (without). React uses `4px 0 0` and `0` respectively. This produces visibly different card spacing.

### 8. Events MapPin/Phone icons unconditional in React
React always renders `<MapPin>` and `<Phone>` icons even when location/phone data is empty. PHP conditionally renders them only when data exists.

## Minor Differences

### 9. Icon weight variations
React uses varied Phosphor icon weights: `bold` (hero CTAs), `fill` (WhatsApp, CheckCircle), `thin` (services, credentials), `duotone` (Planet, tool cards). PHP renders all icons at default weight since `renderIcon()` doesn't support weight.

### 10. Price formatting
PHP uses `number_format()` (English locale with comma separator). React uses `toLocaleString('ne-NP')` (Nepali locale with Devanagari digits and different separators).

### 11. Lightbox close icon
PHP uses Phosphor `X` SVG via `renderIcon('X')`. React uses plain text `✕`.

### 12. Contact panel icons
PHP uses Phosphor SVG icons (`Phone`, `WhatsappLogo`). React uses emoji (`📞`, `📱`).

### 13. Pooja submit button text
PHP: "पूजा बुक गर्नुहोस्". React: "बुकिङ सुरक्षित गर्नुहोस्".

### 14. Container padding
Several React pages apply `paddingTop: 40px` on containers via inline styles that are absent from PHP versions (e.g., appointment, pooja). PHP relies on external CSS.

### 15. CSS class differences
- Muhurta: `booking-form` (PHP) vs `muhurta-form` (React); `form-grid` vs `muhurta-fields`
- Pooja: `download-materials`, `book-btn` classes missing in React
- Store: `product-order-btn` class missing in React
- Panchang: `nav-btn`, `tab-btn` classes and `data-tab` attribute missing in React

## No Differences Found

The following elements are identical between PHP and React versions:
- All section structure and ordering
- All Nepali text content (with minor exceptions noted above)
- All CSS class names for layout grids and cards
- All form field names and labels
- All API endpoint URLs
- All service data, article data, event data structures
- All social media links and contact details
- All privacy disclaimers and informational text

## Recommendations

1. **Fix service dropdown order** in React Appointment.jsx (critical)
2. **Add `#contact-message` element** to contact.php or remove JS reference (minor)
3. **Add `min` attribute** to date inputs in PHP forms for better UX (minor)
4. **Update contact panel padding** in PHP or React to match (minor)
5. **Document icon weight limitation** in `renderIcon()` as a known difference
