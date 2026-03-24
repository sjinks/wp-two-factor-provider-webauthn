---
description: "Use when editing TypeScript in assets/, including WebAuthn login/profile UI flows, browser API handling, AJAX payloads, and JS i18n strings."
name: "Assets TypeScript Guidelines"
applyTo: "assets/**/*.ts"
---
# Assets TypeScript Guidelines

- Treat files in `assets/**/*.ts` as the source of truth; never edit `assets/*.min.js` directly.
- Keep browser-side WebAuthn request/response payload shapes unchanged unless server-side handling is updated in the same change.
- Keep error handling user-safe: show clear user-facing messages and avoid leaking internal details in UI text.
- Prefer existing helpers in `assets/common.ts` for WebAuthn option/credential preparation and DOMException decoding.
- Keep localization strings in `assets/lang.ts` and use `@wordpress/i18n` consistently for new user-facing text.
- Preserve existing script contract globals (for example `ajaxurl` and `tfa_webauthn`) unless corresponding PHP enqueue/localize code is changed.

## Validation Checklist

- Run `npm run lint` after TypeScript changes.
- Run `npm run build` to regenerate committed artifacts in `assets/*.min.js`.
- If login/auth UI behavior changed, run `npm run test:e2e`.

## Related References

- Build config: `rollup.config.mjs`
- TypeScript config: `tsconfig.json`
- Workspace-wide guidance: `.github/copilot-instructions.md`
