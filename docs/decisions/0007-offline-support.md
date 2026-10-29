# 0007: Offline support

**Status:** Accepted

## Context
Many visitors have slow, expensive, or unreliable connections. A site that shows nothing when the connection drops is least useful exactly when people need it. But health information that is out of date can send someone to a closed or moved facility, so offline support must never hide how old a page is.

## Decision
- A service worker (`public/sw.js`) and a web app manifest make the site installable and keep it usable on a weak connection.
- **Network first.** Pages always come from the server when it answers. A saved copy is used only if the network fails, takes more than 4 seconds, or the server returns an error.
- **Saved pages are labelled.** A page shown from a saved copy starts with a notice giving the date it was saved, saying it may be out of date, and telling the visitor to call the facility before travelling.
- **Nothing sensitive is stored.** The maintainer area (`/admin`), the report form (`/report`), and any page showing the "report sent" notice are never stored. Only `GET` requests to this site are handled.
- **Only pages the visitor opened are saved**, in their own browser, up to 40 pages. Nothing is sent to us, and there is no analytics or tracking.
- Searches need a connection. Offline, a visitor can reopen pages they have already viewed, and otherwise sees a plain "You are offline" page that repeats the emergency notice.
- The worker is registered only where the browser supports it, so nothing changes for other browsers.

## Consequences
- Service workers need HTTPS (localhost is exempt). The live site must be served over HTTPS and from the domain root.
- After a change to `public/sw.js`, bump `VERSION` in that file so old caches are cleared.
- Facility data changes are not pushed to saved copies. The saved-copy notice is the safeguard, and it must not be removed.
- The app icons are plain placeholders. Replace them with branded icons of the same sizes when available.
