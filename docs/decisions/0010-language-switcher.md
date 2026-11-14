# 0010: English and Kiswahili language switcher

**Status:** Accepted

## Context
The interface must be usable in English and Kiswahili (see ADR 0001). Health wording in Kiswahili can mislead if it is wrong, so it needs review by a fluent healthcare reviewer (see ADR 0004).

## Decision
- All interface text lives in `lang/en/` and `lang/sw/`. The two folders have the same files and keys; a test fails if they drift apart.
- The header has a language switcher: a labelled navigation with one link per language, each marked with its own `lang`. It is a plain link (`/language/{locale}`), so it works without JavaScript and without a form token.
- The choice is kept in a first-party cookie (`afyalink_locale`, one year, `HttpOnly`, `SameSite=Lax`). Nothing is stored on the server and no session is started for it.
- The allowed languages are listed in `config/afyalink.php` (`locales`). The route only matches those codes, so any other code is a 404 and sets nothing. The `SetLocale` middleware ignores a cookie holding anything else.
- After switching, the visitor goes back to the page they were on. The return address must be a path on this site; anything else goes to the home page, so the switcher cannot be used as an open redirect.
- `<html lang>` follows the active language. Dates use the active language.
- Standard service names are translated in `lang/<locale>/services.php`, keyed by service slug. A service without an entry (for example one added by an import) shows its stored name.
- Kiswahili health wording is marked with a `// healthcare-review` comment on the same line. Those lines must be approved by a fluent healthcare reviewer before Kiswahili is shown to the public.

## Consequences
- To add a language, add its code to `config/afyalink.php`, copy a `lang/` folder, and translate it. The key-parity test covers it.
- Not translated yet: the static offline page (`public/offline.html`), the saved-copy notice in `public/sw.js`, and the assistant's understanding of typed words and its emergency word list, which are English only. The Kiswahili assistant page says so.
