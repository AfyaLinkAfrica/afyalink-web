# 0001: Technology stack

**Status:** Accepted

## Context
AfyaLink Africa must run on modest hosting, load fast on low-end phones and slow connections, and be easy for volunteers to contribute to.

## Decision
- **Backend:** Laravel (PHP), MySQL for local development and production. Automated tests run on in-memory SQLite for speed and zero setup.
- **Frontend:** Blade templates and Bootstrap, with minimal JavaScript. Core pages must work without JavaScript.
- **Testing:** Pest or PHPUnit, with Laravel Pint for code style
- **CI:** GitHub Actions
- **Languages:** English and Kiswahili from the start, using Laravel lang files

## Consequences
- Server-rendered pages are lightweight and accessible by default.
- Cheap shared hosting is viable.
- A separate JavaScript frontend or API-first design is deferred until there is a clear need (for example a mobile app).
- Contributors need PHP, Composer, and MySQL (or SQLite for local testing).
