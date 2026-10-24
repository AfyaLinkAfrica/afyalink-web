# Security Policy

AfyaLink Africa handles health-related information, so security and privacy are central to the project. Thank you for helping keep our users and community safe.

## Reporting a Vulnerability

**Please do not report security vulnerabilities in public issues, discussions, or pull requests.**

Report them privately by email:

- **Email:** dantechdevs@gmail.com
- **Subject line:** `AfyaLink Security`

If GitHub private vulnerability reporting is enabled on the repository, you may use it instead.

### What to include

- A description of the vulnerability and its potential impact
- Steps to reproduce, or a proof of concept that does not expose real user data
- Affected files, routes, or versions, if known
- Your suggested fix, if you have one

**Never include real patient records, credentials, or personal health information in a report.** Use synthetic data to demonstrate the issue.

## What to Expect

- **Acknowledgement:** we aim to respond within 5 business days.
- **Assessment:** we will review the report and keep you informed of progress.
- **Fix and disclosure:** we will work on a fix and coordinate public disclosure with you. Please allow reasonable time to fix the issue before disclosing it.
- **Credit:** with your consent, we will acknowledge your contribution.

AfyaLink Africa is maintained by volunteers, so timelines may vary. We appreciate your patience.

## Scope

In scope:

- The AfyaLink Africa web application and its source code
- Authentication, authorization, and access-control flaws
- Data exposure, injection, and cross-site scripting issues
- Insecure handling of personal or health information

Out of scope:

- Social engineering of maintainers or contributors
- Physical attacks
- Denial-of-service testing against any live deployment
- Vulnerabilities in third-party services we do not control (report these to the vendor)

## Responsible Testing

- Test only against your own local installation or an explicitly authorized test environment.
- Do not access, modify, or delete data that is not yours.
- Do not run automated scans against production systems without written permission.

## Data Protection

Contributors must never commit real patient data, production database dumps, API keys, or private credentials. See [CONTRIBUTING.md](CONTRIBUTING.md) for the full privacy and security rules. The project aims to follow applicable data protection laws, including Kenya's Data Protection Act, 2019.

## Supported Versions

The project is in early development. Security fixes will be applied to the `main` branch until formal releases are published, at which point this section will list supported versions.
