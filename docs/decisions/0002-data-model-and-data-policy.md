# 0002: Data model and data policy

**Status:** Accepted

## Context
The project deals with health-related information. Mistakes here harm people and can break the law.

## Decision
**Core entities (phase 1):** `Facility`, `Service` (many-to-many with Facility), `Report` (user-submitted corrections).

**Data rules:**
1. Development, tests, demos, and screenshots use **synthetic data only**.
2. The project stores **no patient records** in phase 1.
3. Every facility record keeps a `source` and a `verified_at` date. Unverified data is labelled as such in the UI.
4. Real facility data may be imported only after the source's licence is checked and recorded in `docs/data-sources.md`.
5. User reports collect the minimum needed. Contact details are optional.
6. No secrets, `.env` files, or database dumps are ever committed.

## Consequences
- Contributors can work safely without access to sensitive data.
- Any feature touching personal or health data needs a new ADR and maintainer approval first.
- The project aims to comply with Kenya's Data Protection Act, 2019, and similar laws elsewhere.
