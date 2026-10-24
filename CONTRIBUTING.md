# 🤝 Contributing to AfyaLink Africa

Thank you for your interest in contributing to **AfyaLink Africa**!

We are building an open-source community health technology initiative to improve access to reliable health information and healthcare services.

Our mission brings together developers, healthcare professionals, public health researchers, designers, translators, testers, and community organizations.

Whether you are contributing your first line of code or have years of professional experience, we welcome your participation.

**Our goal is to build technology that creates measurable benefits for communities while respecting people's privacy, dignity, and safety.**

---

## 📋 Table of Contents

- [Our Community Principles](#-our-community-principles)
- [Ways to Contribute](#-ways-to-contribute)
- [Before You Start](#-before-you-start)
- [Finding an Issue](#-finding-an-issue)
- [Setting Up Your Development Environment](#-setting-up-your-development-environment)
- [Our Git Workflow](#-our-git-workflow)
- [Coding Guidelines](#-coding-guidelines)
- [Testing Requirements](#-testing-requirements)
- [Submitting a Pull Request](#-submitting-a-pull-request)
- [Reporting Bugs](#-reporting-bugs)
- [Suggesting Features](#-suggesting-features)
- [Healthcare Content Contributions](#-healthcare-content-contributions)
- [Privacy and Security Rules](#-privacy-and-security-rules)
- [Review Process](#-review-process)
- [Recognition and Attribution](#-recognition-and-attribution)
- [Communication](#-communication)
- [Getting Help](#-getting-help)
- [Contact](#-contact)

---

## 🌍 Our Community Principles

Every contributor is expected to follow these principles.

### 1. Respect

Treat every contributor with respect regardless of their nationality, experience, language, gender, background, or professional role.

### 2. Collaboration

Share knowledge, explain technical decisions, review code constructively, and help other contributors learn.

### 3. Community Impact

Prioritize features that address validated community needs rather than adding complexity without a clear benefit.

### 4. Privacy

Never expose patient information, personal health records, credentials, or other sensitive data.

### 5. Safety

Do not introduce unreviewed medical advice, unsupported diagnostic claims, or features that could put users at risk.

### 6. Transparency

Document important decisions, communicate blockers, and be clear about limitations.

### 7. Accessibility

Help make the platform usable for people with different abilities, devices, languages, and levels of digital literacy.

---

## 💡 Ways to Contribute

You do not need to be a programmer to help.

### Backend Development

- Laravel application development.
- Database design and migrations.
- API development.
- Authentication and authorization.
- Application performance.
- Automated testing.

### Frontend Development

- Responsive interfaces.
- Accessible forms and navigation.
- Bootstrap and JavaScript components.
- Mobile usability improvements.
- Translation-ready interface design.

### Mobile Development

- Android application development.
- Offline-capable workflows.
- Secure API integration.
- Notification handling.
- Mobile accessibility.

### Healthcare and Public Health

- Review health education resources.
- Identify credible health information sources.
- Advise on appropriate referral information.
- Review proposed clinical workflows.
- Help define community health needs.
- Evaluate the usefulness of features.

Clinical and public health contributions may require appropriate professional qualifications and review.

### Design and Accessibility

- UI/UX design.
- Wireframes and prototypes.
- Accessibility reviews.
- User research.
- Usability testing.

### Documentation and Translation

- Improve installation instructions.
- Write technical documentation.
- Translate approved materials into Kiswahili and other languages.
- Improve onboarding instructions.
- Prepare contributor guides.

### Testing and Security

- Write automated tests.
- Report reproducible bugs.
- Test supported browsers and devices.
- Identify accessibility problems.
- Review permissions and data protection controls.
- Participate in authorized security reviews.

---

## 🛠️ Before You Start

Please follow these steps:

1. Read the project's `README.md`.
2. Review the roadmap and existing issues.
3. Search for existing discussions about your proposed change.
4. Read the project's license and applicable contribution policies.
5. Ask questions if requirements are unclear.
6. Confirm that your proposed contribution fits the project's scope.

For substantial changes, open an issue or discussion before beginning implementation.

This helps prevent duplicated work and gives maintainers an opportunity to clarify requirements.

---

## 🏷️ Finding an Issue

Issues may use labels such as:

| Label | Meaning |
|---|---|
| `good first issue` | Suitable for someone new to the project |
| `help wanted` | Additional contributors are welcome |
| `bug` | Something is not working as intended |
| `enhancement` | An improvement to an existing feature |
| `feature request` | A proposal for new functionality |
| `documentation` | Documentation improvements |
| `frontend` | User interface work |
| `backend` | Server-side development |
| `mobile` | Mobile application work |
| `testing` | Test coverage and quality assurance |
| `accessibility` | Accessibility improvements |
| `security` | Security-related work requiring appropriate handling |
| `healthcare-review` | Review by a qualified healthcare professional |

Issue labels will be added as the project grows.

If you would like to work on an issue, comment on it to let maintainers know.

Please wait for confirmation before beginning substantial work on an issue assigned to someone else.

---

## 💻 Setting Up Your Development Environment

The initial web application is planned to use Laravel, PHP, and MySQL.

### Prerequisites

Install the following tools:

- Git.
- PHP compatible with the project's Laravel version.
- Composer.
- MySQL or another supported database.
- Node.js and npm if required by the frontend build.
- A code editor of your choice.

Consult the `README.md` and repository configuration files for the exact version requirements.

### 1. Fork the Repository

Visit the official AfyaLink Africa repository on GitHub and select **Fork**.

This creates a copy of the repository under your GitHub account.

### 2. Clone Your Fork

Replace `YOUR-USERNAME` with your GitHub username.

```bash
git clone https://github.com/YOUR-USERNAME/afyalink-web.git
```

### 3. Enter the Project Directory

```bash
cd afyalink-web
```

### 4. Add the Upstream Repository

After confirming the official repository URL, add it as the upstream remote.

```bash
git remote add upstream https://github.com/AfyaLinkAfrica/afyalink-web.git
```

If the upstream remote already exists, do not add it a second time.

### 5. Install Dependencies

```bash
composer install
```

Install frontend dependencies if the project includes a compatible `package.json` file:

```bash
npm install
```

### 6. Configure the Environment

On Linux or macOS:

```bash
cp .env.example .env
```

On Windows Command Prompt:

```cmd
copy .env.example .env
```

Configure your local database using the values in your development environment.

Never copy production credentials into your local environment or commit secrets to GitHub.

### 7. Generate the Application Key

```bash
php artisan key:generate
```

### 8. Run Migrations

```bash
php artisan migrate
```

Use only a local development database or another explicitly authorized test environment.

Do not run migrations against production systems without proper authorization and a reviewed deployment procedure.

### 9. Start the Development Server

```bash
php artisan serve
```

Follow the project's frontend instructions if additional asset compilation is required.

If a command fails, share the error message without exposing credentials or other sensitive information.

---

## 🌿 Our Git Workflow

Please create a separate branch for each focused contribution.

### 1. Update Your Local Main Branch

```bash
git checkout main
git fetch upstream
git pull upstream main
```

If the repository uses a different default branch, use the documented branch name.

### 2. Create a Feature Branch

Examples:

```bash
git checkout -b feature/health-facility-search
```

```bash
git checkout -b fix/mobile-navigation
```

```bash
git checkout -b docs/installation-guide
```

Choose a short, descriptive branch name.

### 3. Make Your Changes

Keep changes focused on the issue you are addressing.

Avoid unrelated refactoring, unnecessary dependency changes, and modifications to unrelated parts of the application.

### 4. Run Tests

Run the relevant automated tests and inspect your changes before committing.

### 5. Commit Your Changes

Use a clear commit message.

Examples:

```bash
git add .
git commit -m "feat: add healthcare facility search"
```

```bash
git commit -m "fix: improve mobile navigation"
```

```bash
git commit -m "docs: improve installation instructions"
```

These examples follow a conventional commit style. Contributors should avoid staging secrets, environment files, generated files, or unrelated changes.

### 6. Push Your Branch

```bash
git push origin feature/health-facility-search
```

Replace the branch name with your actual branch.

### 7. Open a Pull Request

Open GitHub and create a pull request from your branch into the project's default branch.

Provide a clear description of what changed and why.

---

## 📐 Coding Guidelines

Our coding guidelines will evolve as the project matures.

### General Principles

- Write clear, readable, maintainable code.
- Follow established project conventions.
- Keep functions and classes focused.
- Avoid unnecessary duplication.
- Use descriptive names.
- Validate user input.
- Handle errors appropriately.
- Document non-obvious decisions.
- Prefer simple solutions over unnecessary complexity.

### Laravel and PHP

- Follow Laravel conventions.
- Use appropriate validation for incoming data.
- Use authorization policies or equivalent checks where needed.
- Use database transactions when multiple related operations must succeed or fail together.
- Use Eloquent relationships appropriately.
- Avoid exposing internal exceptions or sensitive data.
- Use environment variables for configuration.
- Keep business logic organized and testable.
- Review database queries for performance and security.
- Avoid unnecessary dependencies.

### Database Changes

- Use migrations for schema changes.
- Include appropriate constraints and indexes.
- Avoid destructive changes without a clear migration strategy.
- Consider how existing data will be handled.
- Do not use real patient information in seeders or tests.
- Use factories and synthetic data for development.

### Frontend

- Make pages responsive.
- Use semantic HTML.
- Label form fields clearly.
- Provide meaningful validation messages.
- Consider keyboard navigation and screen readers.
- Keep interfaces understandable on inexpensive smartphones.
- Avoid collecting unnecessary personal information.
- Do not rely on colour alone to communicate important information.

### API Development

- Validate requests.
- Enforce authorization.
- Return consistent response structures.
- Avoid leaking sensitive fields.
- Document API changes.
- Apply appropriate rate limits where necessary.
- Consider versioning for public APIs when needed.

---

## 🧪 Testing Requirements

Contributors should test their changes before submitting a pull request.

For Laravel applications, use the project's configured test framework.

For example:

```bash
php artisan test
```

Where relevant, contributions should include tests for:

- Expected functionality.
- Validation rules.
- Authentication and authorization.
- Error handling.
- Database interactions.
- Privacy and access-control requirements.
- Important regression scenarios.

Frontend contributions should be checked on relevant screen sizes and supported browsers.

Health-related functionality requires additional scrutiny. Passing automated tests does not establish that a feature is clinically appropriate or safe.

Do not claim that a feature has been fully tested if you have not performed the relevant tests.

---

## 🔄 Submitting a Pull Request

Before submitting your pull request, confirm that:

- [ ] Your change addresses an issue or documented requirement.
- [ ] You have checked for existing work on the same problem.
- [ ] Your branch contains only relevant changes.
- [ ] The application runs in your development environment.
- [ ] Relevant automated tests pass.
- [ ] New functionality has appropriate tests.
- [ ] Documentation has been updated where necessary.
- [ ] No credentials or private information have been included.
- [ ] Database changes use appropriate migrations.
- [ ] Health-related changes have been flagged for appropriate review.
- [ ] You have described any known limitations.

### Suggested Pull Request Description

**Summary**

Explain what your contribution changes.

**Related Issue**

Reference the relevant issue, if one exists.

**Motivation**

Explain the problem being addressed.

**Implementation**

Describe the important technical decisions.

**Testing**

List the tests you ran and their results.

**Screenshots**

Include screenshots for relevant user interface changes, using synthetic or otherwise non-sensitive information.

**Risks and Limitations**

Describe any known risks, unresolved questions, or limitations.

Maintainers may request revisions before merging your contribution.

---

## 🐛 Reporting Bugs

When reporting a bug, include:

1. A clear title.
2. A description of the problem.
3. Steps to reproduce it.
4. The expected behaviour.
5. The actual behaviour.
6. The relevant browser, operating system, or software version.
7. Screenshots or logs where appropriate.
8. Whether the issue can be reproduced consistently.

Do not include patient records, credentials, personal health information, or other sensitive data in public bug reports.

### Security Vulnerabilities

**Do not publish exploitable security vulnerabilities in public issues.**

Report suspected vulnerabilities privately by emailing **[dantechdevs@gmail.com](mailto:dantechdevs@gmail.com)** with the subject line "AfyaLink Security", or through the repository's private security reporting channel once it is established.

Please provide enough information to reproduce the issue safely without exposing real user data.

---

## 💬 Suggesting Features

We welcome ideas that address real community health needs.

When suggesting a feature, explain:

- What problem it solves.
- Who would benefit.
- How people currently address the problem.
- What the proposed feature would do.
- Whether it requires personal or health information.
- Any accessibility or low-connectivity considerations.
- Potential safety, privacy, or legal concerns.
- How we could measure whether it works.

Features involving diagnosis, treatment recommendations, clinical decision-making, or patient records require appropriate professional and security review.

Not every proposal will be implemented. Maintainers will prioritize work according to community needs, feasibility, safety, available resources, and project direction.

---

## 🩺 Healthcare Content Contributions

Health education is an important part of AfyaLink Africa, but inaccurate information can cause harm.

Contributors proposing health content should:

- Identify the intended audience.
- Provide credible references.
- Include the publication or review date where appropriate.
- Use clear, accessible language.
- Avoid unsupported medical claims.
- Respect cultural and language differences.
- Identify situations requiring professional medical attention.
- Submit content for appropriate review before publication.

Clinical content must be reviewed and approved through the project's designated process by appropriately qualified healthcare professionals.

Translations of clinical content should preserve its intended meaning and be reviewed for accuracy.

Software developers must not independently approve clinical advice simply because the application can display it.

---

## 🔐 Privacy and Security Rules

Every contributor must follow these requirements.

### Never Commit

- Real patient records.
- Medical histories linked to identifiable people.
- Patient names associated with medical conditions.
- Production database dumps.
- API keys or passwords.
- Private encryption keys.
- Real user authentication tokens.
- Confidential institutional information.
- Any data that you are not authorized to publish.

### Use Synthetic Data

Use fictional names, invented contact details, and synthetic records in tests and demonstrations.

Do not assume that removing a person's name makes health information anonymous.

### Design for Privacy

When developing features that handle personal information:

- Collect only what is necessary.
- Document the purpose of data collection.
- Enforce access controls.
- Protect data during transmission and storage.
- Avoid exposing sensitive information in logs.
- Consider retention and deletion requirements.
- Review third-party integrations.
- Follow applicable data protection laws.

Features involving identifiable health data must not be deployed until appropriate privacy, legal, clinical, and security reviews have been completed.

---

## 🔍 Review Process

Pull requests will be reviewed according to their complexity and risk.

Reviewers may assess:

- Code quality.
- Correctness.
- Security.
- Privacy.
- Test coverage.
- Accessibility.
- Performance.
- Documentation.
- Compatibility with existing functionality.
- Clinical appropriateness where relevant.

Small documentation improvements may require less review than features involving patient information or clinical workflows.

A pull request is not approved merely because it has been submitted or because automated checks pass.

Maintainers may request changes, defer a proposal, or decline a contribution that does not meet project requirements.

---

## 🏆 Recognition and Attribution

We value the work of every contributor.

Subject to contributor consent and applicable privacy preferences, the project may recognize contributions through:

- GitHub contribution history.
- Release notes.
- Contributor acknowledgements.
- Project documentation.
- Community showcases.
- Public project reports.

Recognition does not automatically imply employment, payment, ownership of the project, or an official partnership.

Any paid work, sponsorship, intellectual property arrangement, or formal partnership must be agreed upon separately and transparently.

---

## 🌐 Communication

We aim to build a welcoming international community.

Please:

- Be respectful in discussions.
- Explain technical decisions clearly.
- Ask questions when requirements are unclear.
- Provide constructive feedback.
- Respect different time zones and working schedules.
- Avoid pressuring volunteers to work beyond their availability.
- Document decisions that affect other contributors.

GitHub Issues and Discussions, where enabled, will be our primary public coordination channels. For private matters, partnerships, or questions that do not belong in public, email [dantechdevs@gmail.com](mailto:dantechdevs@gmail.com).

The project may introduce additional communication channels as the community grows.

---

## 🙋 Getting Help

If you are new to open-source development, you are welcome here.

You can start by:

1. Reading the project README.
2. Exploring an issue labelled `good first issue`.
3. Asking questions in a relevant discussion.
4. Improving documentation.
5. Testing the application with synthetic data.
6. Submitting a small, focused contribution.

You do not need to know everything before getting started.

We encourage contributors to learn together and help one another grow.

---

## 📧 Contact

**Dantechdevs Developers** maintains AfyaLink Africa.

- **Email:** [dantechdevs@gmail.com](mailto:dantechdevs@gmail.com)
- **Security reports:** same address, subject line "AfyaLink Security"

Never include patient records, credentials, or other sensitive data in emails or public issues.

---

## ❤️ Our Commitment

AfyaLink Africa is being developed around a simple principle:

**Technology should help people access reliable health information and appropriate healthcare services without compromising their privacy, safety, or dignity.**

Every contribution matters, whether it involves writing code, reviewing health content, improving accessibility, translating educational materials, or helping us understand community needs.

Thank you for helping build AfyaLink Africa.

**Together, we can connect communities and contribute to a healthier future.**
