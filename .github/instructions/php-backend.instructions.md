---
description: "Use when editing PHP plugin code in inc/, views/, index.php, or uninstall.php, including WebAuthn provider logic, AJAX handlers, schema/settings, and rendering helpers."
name: "PHP Backend Guidelines"
applyTo: inc/**/*.php, views/**/*.php, index.php, uninstall.php
---
# PHP Backend Guidelines

- Preserve plugin architecture boundaries: provider/business logic in `inc/`, templates in `views/`, bootstrap in `index.php`.
- Reuse existing helpers (`Utils::render()`, `Utils::get_post_field_as_string()`, `InputFactory`) instead of adding ad-hoc rendering or request parsing patterns.
- Follow the existing error-handling split: expected request/validation exceptions should return actionable user-safe messages, while unexpected failures should return generic internal-error messaging.
- Keep nonce/capability checks early in AJAX handlers before mutating state.
- Keep WebAuthn credential/assertion payloads unmodified before library validation; do not introduce sanitization that can alter those JSON payloads.
- Preserve singleton usage patterns already used by plugin classes.

## Validation Checklist

- Run `composer phpcs` after PHP changes.
- Run `composer psalm` for static analysis.
- If behavior visible in the browser/login flow changed, run `npm run test:e2e`.

## Related References

- Workspace-wide guidance: `.github/copilot-instructions.md`
- Code standards config: `phpcs.xml.dist`
- Static analysis config: `psalm.xml.dist`
