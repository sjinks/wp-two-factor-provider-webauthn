# Copilot Customizations Guide

This document explains how to use the workspace customizations added in this repository.

## What Is Configured

### Workspace Instructions

These are loaded automatically (by file match and/or relevance) and guide normal coding behavior.

- [assets-ts.instructions.md](./instructions/assets-ts.instructions.md)
- [e2e-tests.instructions.md](./instructions/e2e-tests.instructions.md)
- [php-backend.instructions.md](./instructions/php-backend.instructions.md)
- [translations.instructions.md](./instructions/translations.instructions.md)

### Prompt Files

These appear as reusable slash commands in chat.

- [release-checklist.prompt.md](./prompts/release-checklist.prompt.md)
- [security-regression-checklist.prompt.md](./prompts/security-regression-checklist.prompt.md)
- [update-translations.prompt.md](./prompts/update-translations.prompt.md)

### Custom Agents

These appear in the Agent picker (and can be used as subagents when delegated).

- [security-review.agent.md](./agents/security-review.agent.md)
- [security-remediation.agent.md](./agents/security-remediation.agent.md)

### Hooks

Hooks run automatically at lifecycle events (for example, PreToolUse/PostToolUse/SessionStart).

- [block-minified-edits.json](./hooks/block-minified-edits.json)
- [warn-sensitive-path-edits.json](./hooks/warn-sensitive-path-edits.json)
- [warn-missing-request-guards.json](./hooks/warn-missing-request-guards.json)
- [remind-build-after-assets-change.json](./hooks/remind-build-after-assets-change.json)
- [remind-readme-after-version-change.json](./hooks/remind-readme-after-version-change.json)
- [remind-refresh-translations.json](./hooks/remind-refresh-translations.json)
- [sessionstart-repo-policy.json](./hooks/sessionstart-repo-policy.json)

## How To Invoke Prompts

Use either approach:

1. In chat, type `/` and pick the prompt by name.
2. Run `Chat: Run Prompt...` in VS Code command palette and select a prompt.

Common examples:

1. `/release-checklist 2.6.2`
2. `/security-regression-checklist changed files in current branch`
3. `/update-translations PHP admin strings`

## How To Invoke Agents

Use either approach:

1. Open the Agent picker in chat and choose the agent.
2. Ask the default agent to delegate explicitly to one of these agents.

Common examples:

1. Pick `Security Review Agent`, then ask: `Review current diff for auth bypass and XSS risk`.
2. Pick `Security Remediation Agent`, then ask: `Fix finding in inc/class-ajax.php with minimal risk`.

## How Hooks Behave

Hooks run automatically when their event triggers. No manual invocation is required.

Key effects in this repository:

1. Direct edits to generated frontend artifacts are blocked.
2. Sensitive-path edits (vendor/patches) generate warnings.
3. State-changing PHP handler edits without obvious request guards generate warnings.
4. Frontend source edits trigger build reminders.
5. `index.php` version changes trigger readme metadata reminders.
6. String-source i18n edits trigger translation refresh reminders.
7. Session start injects a brief policy message.

## Recommended Daily Workflow

1. Make code changes as usual.
2. Use prompts for repeatable tasks (release checks, security regression, translation refresh).
3. Use security agents for specialized review/remediation work.
4. Treat hook messages as guardrails and follow-up actions.

## Troubleshooting

If a customization does not appear:

1. Confirm the file exists in this repository under `.github/`.
2. Reload VS Code window.
3. Ensure frontmatter is valid YAML (especially `description`, `name`, `applyTo`).
4. For hooks, check JSON validity and script path references in `.github/hooks/`.
