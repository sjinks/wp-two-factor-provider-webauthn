---
description: "Use when strings changed and you need a consistent translation refresh pass (POT/PO/MO/JSON) with a concise status report."
name: "Update Translations"
argument-hint: "changed scope or locale focus"
agent: "agent"
---
Refresh and verify translations for this repository.

Inputs:
- Scope: ${input:changed scope or locale focus}

Workflow:
1. Determine the string-change scope (PHP, TypeScript, views, or mixed).
2. Refresh JS extraction output:
   - `npm run build`
3. Refresh translation catalogs and compiled artifacts:
   - `make -C lang all`
4. Summarize affected translation files in `lang/`.
5. Report any missing or stale locale updates.

Output format:
- Scope analyzed
- Commands run
- Files updated
- Locales impacted
- Issues / blockers
- Next actions

Constraints:
- Prefer tracked files and explicit command results.
- If a command cannot be run, state why and provide the exact follow-up command.
