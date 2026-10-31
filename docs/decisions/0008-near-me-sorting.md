# 0008: "Near me" distance sorting

**Status:** Accepted

## Context
Visitors often want the facility closest to them. Doing that normally means sending the visitor's location to the server, where it would end up in logs, browser history, and backups. A location is sensitive: it can show where someone lives, works, or seeks care.

## Decision
- **The location never leaves the visitor's browser.** The browser's location prompt appears only after the visitor presses "Sort this page by distance from me". The position is used once to work out distances, then discarded. It is not sent to AfyaLink, not put in a URL, and not saved in cookies or browser storage.
- Sorting is done by a short script on the listing page, using the coordinates already shown for each facility. A test guards against network calls and storage in that script.
- **Only the facilities on the current page are sorted.** Sorting every facility would need the location on the server. The page says so next to the button.
- Distances are straight-line estimates, not travel time or road distance. The page says so, and repeats the advice to call the facility before travelling.
- Facilities without a recorded location go last and say "Distance unknown".
- The button appears only when the browser supports location and at least one facility on the page has coordinates. If the visitor refuses, the list stays as it was.
- The visitor can return to name order with one button.

## Consequences
- Distance sorting across all results (not just one page) would need a server-side design and a new decision record covering location handling.
- The location prompt needs a secure connection (HTTPS), apart from localhost.
- Distances are only as good as the recorded coordinates, which are unverified for imported data. The "call ahead" advice is part of the feature and must stay.
