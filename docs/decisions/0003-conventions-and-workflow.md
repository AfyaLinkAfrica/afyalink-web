# 0003: Code conventions and workflow

**Status:** Accepted

## Decision
**Branches:** `main` is protected. Work happens on `feat/<short-name>`, `fix/<short-name>`, `docs/<short-name>`, or `chore/<short-name>`.

**Commits:** Conventional Commits, e.g. `feat(facility): add county filter`. Types: `feat`, `fix`, `docs`, `test`, `refactor`, `chore`.

**Pull requests:**
- One issue per PR, linked with `Closes #123`
- Small and focused (aim for under 400 changed lines)
- Tests included for new behaviour
- CI must pass and one maintainer must approve
- Squash merge

**Code style:** Laravel Pint defaults (PSR-12 based). Run `./vendor/bin/pint` before pushing.

**Structure:** Follow Laravel conventions. Controllers stay thin; shared logic goes in query scopes or action classes. Translatable text always goes in `lang/`, never hard-coded in views.

**Issue claiming:** Comment on the issue to claim it. A maintainer assigns it. Issues with no activity for 7 days return to the pool.

**Review target:** maintainers aim to respond within 72 hours.
