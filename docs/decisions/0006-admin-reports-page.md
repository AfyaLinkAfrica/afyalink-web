# 0006: Maintainer page for reports

**Status:** Accepted

## Context
Decision 0005 left reports readable only with `php artisan reports:list`, which needs server access. Volunteers need a safer, easier way to read reports and mark them resolved. The page shows free-text messages and optional contact details, so access has to be tightly controlled.

## Decision
- A sign-in protected area at `/admin/reports`. It is not linked from any public page.
- **There is no sign-up.** Maintainer accounts are created only by someone with server access, using `php artisan admin:create`. Passwords must be at least 12 characters and are typed at a prompt, never passed as a command argument.
- Every row in the `users` table is a maintainer. Do not add public registration, or any other way to create users, without first adding a role check and a new decision record.
- Sign-in attempts are limited to 5 per minute per visitor address.
- The list shows the facility, date, status and the start of the message. **Contact details appear only on a single report's page**, with a reminder to use them only for a follow-up question.
- The only action is "Mark as resolved". Maintainers cannot edit or export reports. Resolving records `resolved_at`, and the automatic 90-day clean-up from 0005 still applies.
- Admin pages ask search engines and caches not to keep them (`X-Robots-Tag: noindex, nofollow` and `Cache-Control: no-store`).
- All visitor-written text is escaped when shown, so a malicious message cannot run code in a maintainer's browser.

## Consequences
- Anyone who gets a maintainer password can read contact details. Maintainers must use unique passwords and the site must be served over HTTPS.
- The site keeps a session for signed-in maintainers. Check which session driver is configured: with the `database` driver, Laravel stores the visitor address and browser details for every session, which conflicts with the privacy promises in 0005.
- Removing a maintainer means deleting their row in `users`. There is no page for that yet.
