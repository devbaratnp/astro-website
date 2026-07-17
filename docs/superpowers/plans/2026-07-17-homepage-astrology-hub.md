# Homepage Astrology Hub Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the approved responsive astrology feature section to the existing homepage with live Panchang data and working links into the current consultation flow.

**Architecture:** Keep the homepage route and existing layout intact. Add one pure data-normalization helper with Node tests, then render the section directly inside `Home.jsx` using the existing Panchang API and existing routes. Extend the current stylesheet with isolated `astro-hub-*` classes and verify the result through the production build and responsive browser checks.

**Tech Stack:** React 19, React Router, Phosphor Icons, Vite, Node test runner, PHP Panchang API

---

### Task 1: Define the Panchang presentation contract

**Files:**
- Create: `astro-shree-hari-source/src/utils/homeAstrology.js`
- Test: `astro-shree-hari-source/src/utils/homeAstrology.test.js`

- [ ] **Step 1: Write failing tests for complete and partial API data**

Test that the helper returns six stable display items, preserves verified values, and substitutes `उपलब्ध छैन` for null optional values.

- [ ] **Step 2: Run the focused test and verify RED**

Run: `node --test src/utils/homeAstrology.test.js`

Expected: FAIL because `homeAstrology.js` does not exist.

- [ ] **Step 3: Implement the minimal normalizer**

Export `buildHomePanchangItems(panchang)` and return items for tithi, nakshatra, yoga, karana, sunrise, and sunset.

- [ ] **Step 4: Run the focused test and verify GREEN**

Run: `node --test src/utils/homeAstrology.test.js`

Expected: all tests pass.

### Task 2: Add the functional homepage section

**Files:**
- Modify: `astro-shree-hari-source/src/pages/Home.jsx`

- [ ] **Step 1: Add data state and API loading**

Use `getPanchang()` on mount, normalize successful data with `buildHomePanchangItems`, and provide explicit loading and retry/error states.

- [ ] **Step 2: Render the approved homepage hub after the trust bar**

Render a daily Panchang panel and four route cards for Panchang/Rashifal, Kundali, Pooja/Muhurat, and consultation. Use only routes that already exist.

- [ ] **Step 3: Preserve the existing conversion flow**

The primary call to action must link to `/appointment`; feature cards must link to `/panchang`, `/kundali`, `/pooja`, or `/appointment`.

### Task 3: Make the section adaptive and accessible

**Files:**
- Modify: `astro-shree-hari-source/src/styles.css`

- [ ] **Step 1: Add isolated desktop styling**

Use existing maroon, gold, ivory, green, shadow, and border variables. Create a two-column feature area with readable Panchang metrics and interactive cards.

- [ ] **Step 2: Add tablet and mobile layouts**

Collapse the section to one column below 820 px and tool cards to one column below 520 px. Ensure controls remain at least 44 px tall and do not create horizontal overflow at 320 px.

- [ ] **Step 3: Add focus and reduced-motion behavior**

Provide visible `:focus-visible` states and disable decorative transforms under `prefers-reduced-motion: reduce`.

### Task 4: Verify and deploy

**Files:**
- Modify generated deployment assets through the existing build process.

- [ ] **Step 1: Run all unit tests**

Run: `node --test src/utils/*.test.js`

Expected: all tests pass with zero failures.

- [ ] **Step 2: Run the production build**

Run: `npm run build`

Expected: Vite exits successfully and emits the production bundle.

- [ ] **Step 3: Verify locally at responsive widths**

Check 320, 375, 768, 1024, and 1440 px. Confirm the section is visible, live data resolves, links target valid routes, keyboard focus is visible, and no console errors occur.

- [ ] **Step 4: Commit and push only intended files**

Stage the plan, helper, tests, homepage, stylesheet, and generated deploy assets required by the repository workflow. Leave `.superpowers/` and `audit/` untracked.

- [ ] **Step 5: Verify CI/CD and production**

Confirm the workflow succeeds, then check the live homepage, feature links, Panchang API, mobile overflow, and console errors.
