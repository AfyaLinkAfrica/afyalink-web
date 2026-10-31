# 0009: Plain-language facility assistant

**Status:** Accepted

## Context
Many visitors would rather type "child immunisation in Kisumu" than use three dropdowns. A chat-style helper can do that, but an AI chatbot in a health project carries real risks: invented facilities or phone numbers, medical advice nobody has reviewed, and visitors' words, which may describe their health, being sent to an outside company.

## Decision
- The assistant at `/assistant` **only turns a message into the existing search filters** (county, facility type, service). It never writes answers, never gives medical advice, and never invents facilities. Every result still comes from the database through the normal search page.
- It works with plain word lists. **No outside service or AI model is called.** A test guards against adding network calls or logging to the assistant code.
- **What a visitor types is not stored.** The answer is rendered straight from the form post, with no redirect, no session flash, no URL parameter, and no logging. The page tells visitors this.
- The page says what it understood and links to the search. If a message names more than one county, type, or service, it asks which one is meant instead of guessing.
- **Possible emergencies come first.** If wording suggests an emergency, a notice says to contact local emergency services or go to the nearest facility. The notice names no phone numbers, because numbers need reviewer approval (see the emergency numbers card item).
- Sending is limited to 20 messages per minute per visitor address.

## Consequences
- A healthcare reviewer should check the emergency wording list in `EmergencyDetector` and the notice text in `lang/en/assistant.php`. Add this to issue #3.
- Visitors who write in Swahili or Sheng will mostly get "I could not find..." because the word lists are English. Adding Swahili needs a fluent speaker and a reviewer.
- **A real AI model (an LLM) is not part of this decision.** Adding one would send visitors' words to a third party, so it needs a new decision record first. It would have to: ask for clear consent before sending anything, state the provider's data-retention terms, show visitors only the structured filters it returns after checking them against the same allowed lists (never the model's own sentences), and be rate limited and cost-capped. `RuleBasedExtractor` is the one class that would be replaced or wrapped.
