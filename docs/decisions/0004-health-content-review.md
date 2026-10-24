# 0004: Health content review

**Status:** Accepted

## Decision
Developers do not approve health information. Any user-facing health content (education text, disclaimers, symptom or treatment wording, Kiswahili health terms) must be:

1. Based on a cited, reputable source (for example the Kenya Ministry of Health or WHO)
2. Reviewed by a qualified healthcare professional before release
3. Labelled with the source and review date

Pull requests touching health content get the `healthcare-review` label and cannot merge until a reviewer approves. Until a reviewer is on board, health education content stays out of production.

AfyaLink never provides diagnosis or treatment advice and always shows the medical disclaimer and emergency notice.
