---
description: "Use when editing Playwright E2E tests, page objects, WebAuthn test helpers, or Playwright configuration for login/profile/authentication flows."
name: "E2E Playwright Guidelines"
applyTo: tests/e2e/**/*.ts, playwright.config.ts
---
# E2E Playwright Guidelines

- Keep tests Chromium-focused unless the project explicitly expands browser coverage.
- Prefer existing page objects and helpers in `tests/e2e/lib/` over duplicating selectors or login/setup logic in specs.
- Use `test.step()` for multi-stage workflows (login, setup, action, verification) to keep failures diagnosable.
- For WebAuthn scenarios, use virtual authenticator helpers from `tests/e2e/lib/webauthn-helpers.ts`.
- Keep assertions behavior-focused (URL transitions, provider selection, visible errors, credential sign counter changes), not implementation-detail focused.
- Keep settings and credentials sourced from `tests/e2e/e2e-settings.ts` unless a test requires explicit overrides.

## Validation Checklist

- Run targeted E2E while iterating: `npx playwright test tests/e2e/specs/<spec>.spec.ts`.
- Run full E2E suite before finalizing: `npm run test:e2e`.
- If Playwright config changes, confirm CI compatibility with `reporter`, `workers`, and `baseURL` behavior.

## Related References

- E2E config: `playwright.config.ts`
- E2E settings: `tests/e2e/e2e-settings.ts`
- Workspace-wide guidance: `.github/copilot-instructions.md`
