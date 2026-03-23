---
description: "Use when updating translatable strings, translation catalogs, or locale artifacts in lang/, including POT/PO/MO/JSON refresh workflows."
name: "Translations Guidelines"
applyTo: lang/**, assets/**/*.ts, inc/**/*.php, views/**/*.php
---
# Translations Guidelines

- Keep the text domain consistent as `two-factor-provider-webauthn`.
- When changing user-facing strings in PHP or TypeScript, refresh translation artifacts in `lang/` before finalizing.
- Treat `*.pot` as source catalogs and `*.mo` / JS `*.json` as generated artifacts derived from `*.po`.
- Prefer existing i18n patterns (`__`, `_x`, `esc_html__` in PHP and `@wordpress/i18n` in TypeScript) instead of introducing custom translation wrappers.

## Refresh Workflow

- Run `npm run build` to refresh JavaScript POT extraction output.
- Run `make -C lang all` to merge/update PO files and regenerate MO/JSON outputs.
- Review locale files touched by the string changes (for example `ru_RU`, `uk`, `zh_TW`).

## Validation Checklist

- Ensure changed strings are present in the corresponding POT file(s).
- Ensure updated PO files still compile into MO/JSON successfully.
- If release metadata changed, verify language-facing release notes/changelog entries where applicable.

## Related References

- Translation automation: `lang/Makefile`
- JS extraction config: `rollup.config.mjs`
- Workspace-wide guidance: `.github/copilot-instructions.md`
