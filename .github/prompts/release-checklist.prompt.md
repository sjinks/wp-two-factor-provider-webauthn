---
description: "Use when preparing a release or release candidate and you need a consistent pre-release validation checklist and status report."
name: "Release Checklist"
argument-hint: "version/tag or release scope"
agent: "agent"
---
Prepare a release-readiness checklist for this repository and execute the checks where possible.

Inputs:
- Release target: ${input:version/tag or release scope}

Checklist workflow:
1. Confirm repository state and summarize tracked changes relevant to the release.
2. Verify dependencies/installability:
   - `npm ci`
   - `composer install`
3. Verify build and static quality gates:
   - `npm run build`
   - `npm run lint`
   - `composer phpcs`
   - `composer psalm`
4. Verify behavior tests:
   - `npm run test:e2e` for code or behavior changes.
   - For docs-only releases, skip E2E by default and explicitly record the skip reason.
5. Verify release-facing docs and metadata consistency:
   - [README.md](../../README.md)
   - [readme.txt](../../readme.txt)
   - [index.php](../../index.php)
6. Provide a final release decision with clear blockers.

Output format (strict):
- Release target
- Checks run
- Passed
- Failed
- Not run (with reason)
- Risks / follow-ups
- Final decision: `ready` or `not ready`

Constraints:
- Do not edit generated frontend build artifacts directly.
- If any check cannot be run in this environment, state that explicitly and give the exact next command.
- Keep the final report concise and actionable.
