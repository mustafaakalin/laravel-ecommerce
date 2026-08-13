# Security Policy

## Supported Versions

| Version          | Supported          |
| ---------------- | ------------------ |
| latest release   | :white_check_mark: |
| < latest release | :x:                |

Only the latest release is actively supported with security fixes.

## Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability, **please
do NOT open a public GitHub issue**. Instead, report it privately to the
maintainer so it can be fixed before disclosure.

How to report:

- **Preferred:** Open a private vulnerability report via
  [GitHub's private vulnerability reporting](https://docs.github.com/en/code-security/security-advisories/guidance-on-reporting-and-writing/privately-reporting-a-security-vulnerability)
  on the repository's **Security** tab.
- **Alternative:** Email the maintainer directly (see the repository owner
  profile). Please include the words `SECURITY` in the subject line.

When reporting, include:

1. The affected version(s) and a clear description of the issue.
2. Steps to reproduce (preferably a minimal proof-of-concept).
3. The potential impact and any suggested mitigation.

You should receive an acknowledgment within **7 days**. We will work with you
to understand the scope of the issue and coordinate a fix. We ask that you
refrain from publicly disclosing the vulnerability until a fix is released.

## Disclosure Policy

Once a fix is released, we will publish a security advisory describing the
vulnerability, the affected versions, and the fixed version.

## Additional Notes

When deploying this application:

- Always change the `APP_KEY` and every credential in your `.env`.
- Never commit real credentials, API keys, or the production `.env` file.
- Keep PHP, Composer, and npm dependencies up to date.
