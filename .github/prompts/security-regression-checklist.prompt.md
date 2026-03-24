---
description: "Use when you need a security regression pass on changed files before merge or release."
name: "Security Regression Checklist"
argument-hint: "diff scope, files, or threat focus"
agent: "Security Review Agent"
---
Run a focused security regression review for this repository using the requested scope.

Inputs:
- Scope: ${input:diff scope, files, or threat focus}

Checklist:
1. Determine changed files for the selected scope (prefer tracked diff and include PHP, views, and auth-related TypeScript paths when present).
2. Prioritize high-risk areas first: authentication flow, authorization checks, request validation, output escaping, and state-changing handlers.
3. Trace concrete source -> sink paths for each candidate risk.
4. Classify findings by severity and include proof/evidence.
5. If no findings are confirmed, report residual risks and verification gaps.

Output format:
- Scope reviewed
- Files reviewed
- Findings (ordered by severity)
- Open questions / assumptions
- Residual risks and test gaps
- Merge recommendation: `safe to merge` or `needs fixes`

Constraints:
- Prefer evidence from tracked files and current diff state.
- Do not report speculative vulnerabilities without a realistic impact path.
