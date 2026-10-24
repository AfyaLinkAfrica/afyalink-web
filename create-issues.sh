#!/usr/bin/env bash
# Creates labels and the first 10 issues. Requires GitHub CLI (gh) and `gh auth login`.
# Usage: ./create-issues.sh OWNER/REPO   e.g. ./create-issues.sh AfyaLinkAfrica/afyalink-web
set -euo pipefail
REPO="${1:?Usage: $0 OWNER/REPO}"

gh label create "good first issue" --color 7057ff --repo "$REPO" --force
gh label create "help wanted" --color 008672 --repo "$REPO" --force
gh label create "backend" --color 1d76db --repo "$REPO" --force
gh label create "frontend" --color fbca04 --repo "$REPO" --force
gh label create "testing" --color 0e8a16 --repo "$REPO" --force
gh label create "accessibility" --color 5319e7 --repo "$REPO" --force
gh label create "security" --color b60205 --repo "$REPO" --force
gh label create "healthcare-review" --color d93f0b --repo "$REPO" --force
gh label create "documentation" --color 0075ca --repo "$REPO" --force

gh issue create --repo "$REPO" --title 'Add GitHub Actions CI for tests and code style' --label 'good first issue,testing,backend' --body-file - <<'EOF'
Run the test suite and Laravel Pint on every pull request.

**Acceptance criteria**
- [ ] Workflow triggers on push and pull_request to main
- [ ] Runs `php artisan test` against a SQLite or MySQL service
- [ ] Runs `./vendor/bin/pint --test`
- [ ] Add a CI status badge to README.md

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Create Facility model, migration, and factory' --label 'good first issue,backend' --body-file - <<'EOF'
Model a healthcare facility. Fields: name, type (hospital, health centre, dispensary, clinic), county, sub_county, address, phone, latitude, longitude, source, verified_at.

**Acceptance criteria**
- [ ] Migration with sensible indexes (county, type, name)
- [ ] Eloquent model with casts and fillable fields
- [ ] Factory that generates clearly fictional facilities
- [ ] Seeder creating about 50 synthetic facilities
- [ ] Unit tests for the model and factory

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Add medical disclaimer and emergency notice to the layout' --label 'good first issue,frontend,healthcare-review' --body-file - <<'EOF'
Every page must show that AfyaLink is not a replacement for professional care and direct users to emergency services in an emergency.

**Acceptance criteria**
- [ ] Footer disclaimer on all pages
- [ ] Emergency notice that cannot be dismissed
- [ ] Strings stored in lang files for translation
- [ ] Wording flagged for review by a qualified healthcare professional before release

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Facility listing page with pagination' --label 'good first issue,frontend' --body-file - <<'EOF'
Build a mobile-first Blade and Bootstrap page listing facilities.

**Acceptance criteria**
- [ ] Route, controller, and view
- [ ] Paginated list (15 per page)
- [ ] Works well on a low-end phone and slow connection
- [ ] Feature test for the route

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Search and filter facilities by name, county, and type' --label 'backend,frontend,help wanted' --body-file - <<'EOF'
Let users narrow the directory.

**Acceptance criteria**
- [ ] Query scopes on the Facility model
- [ ] Filter form that works without JavaScript
- [ ] Filters preserved across pagination
- [ ] Tests covering each filter and combinations

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Add Service model and filter facilities by service offered' --label 'backend,help wanted' --body-file - <<'EOF'
Facilities offer services such as maternity, immunisation, or outpatient care. Model this as a many-to-many relationship.

**Acceptance criteria**
- [ ] Service model, pivot table, migration, factory
- [ ] Seed synthetic services
- [ ] Add a service filter to the listing page
- [ ] Document the data model in docs/

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Facility detail page with contact info and last-verified date' --label 'good first issue,frontend' --body-file - <<'EOF'
Show one facility's details, services, and when its information was last verified.

**Acceptance criteria**
- [ ] Route and view using route-model binding
- [ ] Clickable phone link and map link when coordinates exist
- [ ] Clear note when information is unverified
- [ ] 404 page for unknown facilities

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Add a form to report incorrect information' --label 'backend,help wanted,security' --body-file - <<'EOF'
Users can flag wrong facility details for maintainer review.

**Acceptance criteria**
- [ ] Report model and migration (facility_id, message, contact optional)
- [ ] Server-side validation and rate limiting
- [ ] Spam protection (honeypot or similar)
- [ ] No personal data collected beyond what is needed
- [ ] Tests including validation and throttling

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Add English and Kiswahili language switcher' --label 'good first issue,frontend,accessibility' --body-file - <<'EOF'
Make the interface translation-ready and ship English and Kiswahili.

**Acceptance criteria**
- [ ] Move all UI strings into lang files
- [ ] Language switcher that persists the choice
- [ ] Translate the listing, search, and detail pages
- [ ] Kiswahili health wording flagged for review by a fluent healthcare reviewer

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF

gh issue create --repo "$REPO" --title 'Accessibility review of the directory pages' --label 'good first issue,accessibility,testing' --body-file - <<'EOF'
Check the pages against basic WCAG 2.1 AA practices.

**Acceptance criteria**
- [ ] Labels on every form field
- [ ] Keyboard navigation and visible focus states
- [ ] Colour contrast and no colour-only meaning
- [ ] Test with a screen reader and on a small screen
- [ ] Report findings as separate issues or fix directly

Use synthetic data only. Never commit real patient information. See CONTRIBUTING.md.
EOF
