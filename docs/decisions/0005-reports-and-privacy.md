# 0005: Incorrect-information reports and privacy

**Status:** Accepted

## Context
Facility data goes out of date, and visitors are the best source of corrections. A public form also invites spam and accidental personal data, so it needs strict limits.

## Decision
- Visitors can report a problem on any facility page. A report is a free-text message plus an optional contact detail.
- **No accounts, no IP addresses, and no tracking are stored** with a report. A test guards against adding an IP column.
- The contact detail is optional, is never shown on the site, and is used only to ask a follow-up question.
- The form tells visitors not to include personal or medical details.
- **Spam protection:** a hidden honeypot field, server-side validation (message 10 to 1000 characters), and a limit of 5 submissions per 10 minutes per visitor address (held in the cache, not in the database).
- Reports are read by maintainers with `php artisan reports:list` and closed with `php artisan reports:resolve <id>`. There is no public or admin web page for reports yet.
- **Retention:** resolved reports are deleted automatically 90 days after they were resolved. `reports:resolve` records the resolution time, and `php artisan reports:prune` deletes expired reports. The scheduler runs it daily. Reports that are still new are never deleted by this clean-up. Maintainers can preview it with `php artisan reports:prune --dry-run`.

## Consequences
- A maintainer with server access has to check reports regularly. An admin page can come later, behind authentication.
- Behind a reverse proxy, the application must be configured to trust the proxy, otherwise all visitors share one address and the rate limit hits everyone together.
- Any change that stores more personal data from reports needs a new decision record first.
- **The host must run Laravel's scheduler**, with a cron entry that runs `php artisan schedule:run` every minute. Without it the automatic clean-up never happens, and `reports:prune` has to be run by hand.
- Reports resolved before the clean-up existed were given a resolution date equal to their last update, so their 90 days count from then.
