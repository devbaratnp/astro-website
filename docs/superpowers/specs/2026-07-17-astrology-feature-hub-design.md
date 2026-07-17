# Astrology Feature Hub — Design Specification

Date: 2026-07-17
Status: Approved visual direction; implementation scope pending final review

## 1. Objective

Extend Astro Shree Hari with a responsive astrology feature hub that feels native to the existing maroon, gold, ivory, and green website. The release should make the site more useful before a visitor books a consultation, while keeping the existing appointment, e-pooja, payment, and administration flows intact.

The approved concept establishes the visual direction: a calm editorial layout, compact daily astrology summary, interactive tool cards, strong trust signals, and clear consultation calls to action.

## 2. Release 1 Scope

### 2.1 Astrology tools hub

Add a new top-level “ज्योतिष उपकरण” destination and link it from the existing navigation without overcrowding the header. On narrow screens it appears as a normal item in the current mobile menu.

The hub contains four discoverable areas:

1. आजको पञ्चाङ्ग र राशिफल
2. निःशुल्क उपकरण
3. शुभ मुहूर्त
4. ज्योतिष ज्ञान

Each area has a concise preview and a route to its full experience. Consultation prompts continue to use the existing appointment route.

### 2.2 Enhanced Panchang and horoscope

Upgrade the existing Panchang page rather than creating a competing page.

The page includes:

- previous day, today, next day, and calendar date selection;
- AD date plus BS date when a verified conversion is available;
- tithi, nakshatra, yoga, karana, sunrise, and sunset;
- a twelve-sign horoscope selector with keyboard-accessible controls;
- clear loading, unavailable, retry, and partial-data states;
- a short accuracy/data-source notice;
- an appointment call to action after the daily reading.

The existing Panchang API remains the source of truth. Database/admin-entered values override calculated fallbacks. The current day-of-year approximations in `backend/lib/Panchang.php` must not be described as astronomically precise. Any field that cannot be verified is omitted or clearly labelled as a general estimate.

### 2.3 Functional numerology tool

Add a client-side numerology calculator using a visitor’s name and date of birth. It returns:

- birth number;
- life-path number;
- name number when a name is supplied;
- concise interpretations;
- a privacy note explaining that entered values are processed locally and are not saved;
- a consultation call to action.

The calculation is implemented as a pure, testable module. Only logic extracted from the desktop reference that can be understood, isolated, and covered by fixtures is reused.

### 2.4 Functional compatibility tool

Add a basic compatibility flow for two people with names and birth details. Release 1 provides a clearly labelled preliminary compatibility overview and does not claim to be a complete Vedic Guna Milan unless all required astronomical inputs and rules are validated.

The result includes:

- a transparent explanation of what was evaluated;
- category-level outcomes instead of an unexplained score;
- a disclaimer that the result is informational;
- a consultation call to action for a full matching assessment.

If validated Guna Milan logic and fixtures can be safely extracted from `D:\desktop-app-jyotish\index.html`, the full score may be enabled. Otherwise, the UI must retain the preliminary label and never fabricate precision.

### 2.5 Muhurat and knowledge content

Add lightweight, indexable content pages for:

- upcoming Muhurat categories and dates;
- Navagraha reference information;
- selected astrology articles;
- links to related services and appointment booking.

Content uses the existing article/admin data when available. Static seed content must be reviewed before publication. Release 1 does not include a fully automated Muhurat calculation engine.

### 2.6 Trust section

Add a restrained trust section to the home page and tools hub containing only verified information:

- astrologer credentials and experience;
- real service categories;
- real testimonials with permission;
- real consultation or client counts, if supplied.

No invented ratings, testimonials, credentials, or counters may ship. Missing items remain hidden rather than displaying placeholders.

## 3. Deferred Scope

The following require separate validation and are intentionally deferred from Release 1:

- astronomically precise birth-chart generation;
- downloadable Kundali PDF with authoritative chart calculations;
- complete automated Ashtakoota/Guna Milan without validated ephemeris data;
- Vastu analysis engine;
- fully automated annual Muhurat engine;
- rewriting or embedding the 3.8 MB desktop HTML application.

These may be delivered in Release 2 after calculation sources, fixtures, and domain review are agreed.

## 4. User Flow

1. A visitor discovers the tools hub from the header, home page, or a search result.
2. The visitor sees today’s Panchang summary and chooses a tool or knowledge section.
3. A tool collects only the information required for that calculation.
4. Validation happens inline before calculation.
5. Results explain both the outcome and its limits.
6. A contextual call to action sends the visitor to the existing appointment route with the relevant service selected when supported.
7. Back navigation returns to the same hub state where practical.

No new checkout or booking flow is introduced.

## 5. Information Architecture and Routes

Proposed routes:

- `/astro-tools` — feature hub
- `/panchang` — upgraded existing page
- `/astro-tools/numerology` — numerology calculator
- `/astro-tools/compatibility` — preliminary or validated matching tool
- `/muhurat` — Muhurat listing/content
- `/knowledge` — astrology knowledge index
- `/knowledge/:slug` — article or Navagraha detail

Existing routes and deep links remain valid. Route-level lazy loading should be used if supported by the current application structure.

## 6. Frontend Architecture

Keep route pages thin and isolate reusable behavior:

- page components for hub, numerology, compatibility, Muhurat, and knowledge;
- shared cards, result panels, date navigation, notices, and appointment CTA components;
- pure calculation modules for numerology and any validated compatibility logic;
- a small content adapter for articles and verified trust data;
- extensions to the existing API service rather than direct fetch calls scattered across pages.

Styles extend the current design tokens and responsive patterns. The implementation must not introduce a separate visual system or a second global reset.

## 7. Backend and Data Rules

- Extend the Panchang response with optional `yoga`, `karana`, and `bs_date` fields.
- Preserve compatibility with existing clients by keeping all current response fields.
- Treat curated database values as higher confidence than fallback calculations.
- Return `null` for unavailable optional values rather than plausible-looking fabricated data.
- Reuse the current article model/API for knowledge content where feasible.
- Avoid saving numerology or compatibility form data in Release 1.
- Do not turn tool usage into an appointment record; only an explicit booking action may do so.

## 8. Responsive and Accessibility Requirements

- Mobile-first layouts from 320 px upward.
- Single-column cards on phones, two columns where space permits, and balanced multi-column desktop layouts.
- No horizontal scrolling at 320 px.
- Touch targets of at least 44 by 44 CSS pixels.
- Visible keyboard focus and complete keyboard operation for tabs, sign selectors, date navigation, and forms.
- Semantic headings, labels, validation messages, and status announcements.
- Respect reduced-motion preferences.
- Maintain readable contrast across ivory, maroon, gold, and green surfaces.

## 9. Error and Empty States

Every API-backed section must handle:

- initial loading;
- slow connection;
- network or server error with retry;
- missing fields;
- no content for a selected date;
- stale cached data when detectable.

Calculation forms must show field-level validation and preserve valid input after an error. Unexpected failures are caught by the existing React error boundary without blanking the whole site.

## 10. Testing and Verification

### Automated

- Unit tests for numerology calculations and edge cases.
- Fixture tests for any compatibility logic extracted from the desktop reference.
- API tests for full and partial Panchang responses.
- Navigation and route rendering tests where the current test stack supports them.
- Production build in CI.

### Manual

- Test Chrome/Edge desktop and mobile-sized viewports at 320, 375, 768, 1024, and 1440 px.
- Verify keyboard navigation and focus visibility.
- Verify existing appointment, e-pooja, payment, and admin flows are unchanged.
- Verify live asset loading and browser console after deployment.
- Compare all accuracy-sensitive sample outputs against approved reference fixtures before enabling authoritative wording.

## 11. Deployment Strategy

Deliver in small, reversible increments:

1. shared UI and navigation;
2. upgraded Panchang contract and page;
3. numerology tool;
4. compatibility tool with the correct confidence label;
5. Muhurat, knowledge, and verified trust content;
6. live smoke test after the existing CI/CD deployment completes.

New routes should be deployable without changing the existing booking or payment database schema. Any API extension must remain backward compatible.

## 12. Acceptance Criteria

Release 1 is complete when:

- the feature hub is reachable from existing desktop and mobile navigation;
- all new pages match the existing Astro Shree Hari visual language;
- the Panchang page supports date navigation, twelve horoscope signs, and honest partial-data handling;
- numerology works locally and passes deterministic unit tests;
- compatibility results state exactly what logic was used and do not overclaim accuracy;
- all calls to action enter the existing appointment flow correctly;
- verified content replaces or hides all mockup-only trust content;
- new pages work without horizontal overflow at 320 px;
- existing production flows still pass smoke testing;
- the production browser console has no new errors.
