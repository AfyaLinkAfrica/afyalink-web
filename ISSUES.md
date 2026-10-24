# First 10 issues for AfyaLink Africa

Labels are listed after each title. Create them first, then open the issues (or run `create-issues.sh`).

## 1. Add GitHub Actions CI for tests and code style
**Labels:** `good first issue`, `testing`, `backend`

Run the test suite and Laravel Pint on every pull request.

**Acceptance criteria**
- [ ] Workflow triggers on push and pull_request to main
- [ ] Runs `php artisan test` against a SQLite or MySQL service
- [ ] Runs `./vendor/bin/pint --test`
- [ ] Add a CI status badge to README.md

## 2. Create Facility model, migration, and factory
**Labels:** `good first issue`, `backend`

Model a healthcare facility. Fields: name, type (hospital, health centre, dispensary, clinic), county, sub_county, address, phone, latitude, longitude, source, verified_at.

**Acceptance criteria**
- [ ] Migration with sensible indexes (county, type, name)
- [ ] Eloquent model with casts and fillable fields
- [ ] Factory that generates clearly fictional facilities
- [ ] Seeder creating about 50 synthetic facilities
- [ ] Unit tests for the model and factory

## 3. Add medical disclaimer and emergency notice to the layout
**Labels:** `good first issue`, `frontend`, `healthcare-review`

Every page must show that AfyaLink is not a replacement for professional care and direct users to emergency services in an emergency.

**Acceptance criteria**
- [ ] Footer disclaimer on all pages
- [ ] Emergency notice that cannot be dismissed
- [ ] Strings stored in lang files for translation
- [ ] Wording flagged for review by a qualified healthcare professional before release

## 4. Facility listing page with pagination
**Labels:** `good first issue`, `frontend`

Build a mobile-first Blade and Bootstrap page listing facilities.

**Acceptance criteria**
- [ ] Route, controller, and view
- [ ] Paginated list (15 per page)
- [ ] Works well on a low-end phone and slow connection
- [ ] Feature test for the route

## 5. Search and filter facilities by name, county, and type
**Labels:** `backend`, `frontend`, `help wanted`

Let users narrow the directory.

**Acceptance criteria**
- [ ] Query scopes on the Facility model
- [ ] Filter form that works without JavaScript
- [ ] Filters preserved across pagination
- [ ] Tests covering each filter and combinations

## 6. Add Service model and filter facilities by service offered
**Labels:** `backend`, `help wanted`

Facilities offer services such as maternity, immunisation, or outpatient care. Model this as a many-to-many relationship.

**Acceptance criteria**
- [ ] Service model, pivot table, migration, factory
- [ ] Seed synthetic services
- [ ] Add a service filter to the listing page
- [ ] Document the data model in docs/

## 7. Facility detail page with contact info and last-verified date
**Labels:** `good first issue`, `frontend`

Show one facility's details, services, and when its information was last verified.

**Acceptance criteria**
- [ ] Route and view using route-model binding
- [ ] Clickable phone link and map link when coordinates exist
- [ ] Clear note when information is unverified
- [ ] 404 page for unknown facilities

## 8. Add a form to report incorrect information
**Labels:** `backend`, `help wanted`, `security`

Users can flag wrong facility details for maintainer review.

**Acceptance criteria**
- [ ] Report model and migration (facility_id, message, contact optional)
- [ ] Server-side validation and rate limiting
- [ ] Spam protection (honeypot or similar)
- [ ] No personal data collected beyond what is needed
- [ ] Tests including validation and throttling

## 9. Add English and Kiswahili language switcher
**Labels:** `good first issue`, `frontend`, `accessibility`

Make the interface translation-ready and ship English and Kiswahili.

**Acceptance criteria**
- [ ] Move all UI strings into lang files
- [ ] Language switcher that persists the choice
- [ ] Translate the listing, search, and detail pages
- [ ] Kiswahili health wording flagged for review by a fluent healthcare reviewer

## 10. Accessibility review of the directory pages
**Labels:** `good first issue`, `accessibility`, `testing`

Check the pages against basic WCAG 2.1 AA practices.

**Acceptance criteria**
- [ ] Labels on every form field
- [ ] Keyboard navigation and visible focus states
- [ ] Colour contrast and no colour-only meaning
- [ ] Test with a screen reader and on a small screen
- [ ] Report findings as separate issues or fix directly
