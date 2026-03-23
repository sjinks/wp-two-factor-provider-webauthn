# Project Guidelines

## Build And Test
- Install dependencies with `npm ci` and `composer install`.
- Build frontend assets with `npm run build`.
- Use `npm run watch` while iterating on TypeScript files.
- Run JavaScript linting with `npm run lint` and auto-fix with `npm run lint:fix`.
- Run PHP coding standards with `composer phpcs` and auto-fix with `composer phpcs:fix`.
- Run static analysis with `composer psalm`.
- Run end-to-end tests with `npm run test:e2e` (Docker required).

## Architecture
- This repository is a WordPress plugin that adds a WebAuthn provider for the Two Factor plugin.
- `index.php` is the plugin entry point and bootstraps `Plugin::instance()`.
- `inc/` contains core PHP classes (provider logic, schema, settings, ajax handlers, utility classes).
- `assets/` contains TypeScript sources; built artifacts are committed as `assets/*.min.js`.
- `views/` contains PHP templates rendered by helper utilities.
- `tests/e2e/` contains Playwright tests.

## Conventions
- Do not edit minified files directly; update TypeScript in `assets/*.ts` and rebuild.
- Keep WebAuthn response payload handling intact: `webauthn_response` must remain unmodified before WebAuthn library validation.
- Follow existing error-handling pattern: distinguish expected request/validation exceptions from unexpected internal failures.
- Use existing rendering/helpers instead of ad-hoc HTML output where possible (for example `Utils::render()` and `InputFactory`).
- Preserve current singleton usage patterns used by plugin bootstrap and provider classes.

## CI And Dependency Pitfalls
- Composer patches in `patches/` are required for current dependency compatibility; dependency upgrades may require patch updates.
- GitHub Actions uses hardened runner egress controls; when adding new dependency download hosts, update workflow allowlists.
- CI and E2E workflows use different PHP version formats (`8.x` in setup-php matrix vs `php8.x` in E2E Docker image tags).

## Link Instead Of Duplicate
- Project overview and usage: [README.md](../README.md)
- Changelog and WordPress.org metadata: [readme.txt](../readme.txt)
- Security policy: [SECURITY.md](../SECURITY.md)
- Build pipeline: [.github/workflows/ci.yml](workflows/ci.yml)
- Lint pipeline: [.github/workflows/lint.yml](workflows/lint.yml)
- Static analysis pipeline: [.github/workflows/static-code-analysis.yml](workflows/static-code-analysis.yml)
- E2E pipeline: [.github/workflows/e2e.yml](workflows/e2e.yml)