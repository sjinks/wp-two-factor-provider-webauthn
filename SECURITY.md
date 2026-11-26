# Security Policy

## Reporting Security Issues

We take security seriously. We appreciate your efforts to disclose your findings responsibly and will make every effort to acknowledge your contributions.

To report a security vulnerability, please email us at [volodymyr@wildwolf.name](mailto:volodymyr@wildwolf.name). Please do not create public GitHub issues for security vulnerabilities.

Please include the following in your report.

- Description of the vulnerability
- Steps to reproduce the vulnerability
- Versions affected
- Any additional information that might be useful

## Response Time

We will acknowledge receipt of your vulnerability report within 24 hours, excluding weekends and holidays. We will then work diligently to assess and address the vulnerability promptly.

We prioritize the handling of security vulnerabilities and strive to provide updates and fixes promptly.

## Confidentiality

We request that you only disclose the details of the vulnerability or related correspondence once we have had an opportunity to address the issue. Once the vulnerability has been resolved, we will coordinate with you on an appropriate timeline for public disclosure.

## Scope

Report vulnerabilities in this WordPress WebAuthn provider, including issues in how it configures, patches, bundles, or integrates dependencies. For a vulnerability confined to an upstream dependency, contact that dependency’s maintainer.

A WebAuthn assertion must authenticate the intended WordPress account. Credential enrollment, renaming, and revocation must enforce permission to edit the target account and Two Factor’s revalidation policy for the acting user. See the [threat model](https://github.com/sjinks/wp-two-factor-provider-webauthn/blob/master/docs/threat-model.md) for trust boundaries and assumptions.

## Bug Bounty

We do not currently offer a bug bounty program, but we sincerely appreciate and acknowledge the contributions of security researchers who responsibly disclose vulnerabilities to us.

Thank you for helping to keep this project secure!
