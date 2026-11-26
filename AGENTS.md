# Repository instructions

## Threat model

Review [the threat model](docs/threat-model.md) whenever a change affects a trust boundary or security control. This includes changes to login or credential management, authorization or revalidation, ceremony state, credential storage or migration, browser handling of untrusted data, deployment settings, security-relevant dependencies, and release packaging. Update the affected sections and source references in the same change when behavior or assumptions change. Distinguish source-verified controls from deployment assumptions and open scenarios.
