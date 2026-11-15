# 0010: English and Kiswahili language switcher

**Status:** Accepted

## Context
The interface must be usable in English and Kiswahili (see ADR 0001). Health wording in Kiswahili can mislead if it is wrong, so it needs review by a fluent healthcare reviewer (see ADR 0004).

## Decision
- All interface text lives in `lang/en/` and `lang/sw/`. The two folders have the same files and keys; a test fails if they drift apart.
- The header has a language switcher: a labelled navigation with one link per language, each marked with its own `lang`. It is a plain link (`/language/{locale}`), so it works without JavaScript and without a form token.
- The choice is kept in a first-party cookie (`afyalink_locale`, one year, `HttpOnly`, `SameSite=Lax`). Nothing is stored on the server and no session is started for it.
- The cookie is marked `Secure` on HTTPS requests the app sees as HTTPS. Behind a proxy that ends TLS, set `trustProxies` (`bootstrap/app.php`) so the app sees the original scheme, or set `SESSION_SECURE_COOKIE=true`; otherwise the cookie is sent without `Secure`. The same holds for the session cookie.
- The allowed languages are listed in `config/afyalink.php` (`locales`). Any other code is a 404 and sets nothing. The `SetLocale` middleware ignores a cookie holding anything else.
- After switching, the visitor goes back to the page they were on. The return address must be a path on this site; anything else goes to the home page, so the switcher cannot be used as an open redirect.
- `<html lang>` follows the active language. Dates use the active language.
- Standard service names are translated in `lang/<locale>/services.php`, keyed by service slug. A service without an entry (for example one added by an import) shows its stored name.
- Kiswahili health wording is marked with a `// healthcare-review` comment on the same line. Those lines must be approved by a fluent healthcare reviewer before Kiswahili is shown to the public. For that reason Kiswahili is off by default: set `AFYALINK_ENABLE_SWAHILI=true` once the review is done. While it is off, `en` is the only locale, the switcher is not shown and `/language/sw` is a 404.
- The service worker answers the `/language/<code>` request itself and deletes all saved pages once the server has replied with the redirect, that is after the cookie is set. A saved page does not record its language and the worker cannot read the `HttpOnly` cookie, so this stops an old-language copy from being shown after a switch. If the request fails (the visitor is offline) or is refused (a 404), nothing is deleted and the saved pages stay usable.
- The worker also counts language changes and does not save a page whose fetch started before the latest change, so a slow background refresh cannot write an old-language page back after the deletion. This is best effort: the count lives in memory and starts again if the browser restarts the worker.
- Service names are sorted by the name shown in the active language, after fetching.

## Consequences
- To add a language, add its code to `config/afyalink.php`, copy a `lang/` folder, and translate it. The key-parity test covers it.
- Not translated yet: the static offline page (`public/offline.html`), the saved-copy notice in `public/sw.js`, and the assistant's understanding of typed words and its emergency word list, which are English only. The Kiswahili assistant page says so.
- Responses carry `Vary: Cookie`, so a shared cache (a CDN or reverse proxy) keeps one copy per language choice instead of serving one language to everybody.
- `/language/<code>` changes state on a GET request. The effect is small (the visitor's language flips) and the link is `nofollow`, so this is accepted.
