# Contributing to Laravel E-Commerce 1

First off, thanks for taking the time to contribute! 🎉

The following is a set of guidelines for contributing to this project. These
are mostly guidelines, not rules. Use your best judgment, and feel free to
propose changes to this document in a pull request.

## Code of Conduct

This project and everyone participating in it is governed by a simple code of
conduct: **be kind and respectful to others**. By participating, you are
expected to uphold this. Please report unacceptable behavior to the maintainer
through a private channel.

## How Can I Contribute?

### Reporting Bugs

Before creating a bug report, please double-check the [issues](../../issues)
for duplicates. When creating a bug report, include:

- A clear and descriptive title.
- The exact steps to reproduce the bug.
- What you expected to happen vs. what actually happened.
- Your environment: OS, PHP version, Docker/Podman versions.
- Any relevant logs or stack traces.

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating one, please
describe the current behavior, the desired behavior, and why the change would
be useful.

### Pull Requests

1. Fork the repository and create your branch from `main`.
2. If you've added code, add tests that cover it.
3. Ensure the code style follows Laravel conventions and **Laravel Pint**:

   ```bash
   ./vendor/bin/pint
   ```

4. Make sure the test suite passes:

   ```bash
   php artisan test
   ```

5. Issue that pull request!

## Development Setup

Follow the [Quick Start](README.md#-quick-start-docker--laravel-sail) guide in
the README to get a working environment, then:

```bash
composer install
npm install
./vendor/bin/sail up -d
./vendor/bin/sail php artisan migrate:fresh --seed
```

## Style Guide

- Follow [Laravel's coding style](https://laravel.com/docs/contributions#coding-style)
  and the conventions already used in the codebase.
- Always use `Schema` migrations for database changes.
- Add a feature test for every new behavior.

## Commit Messages

Use clear, imperative commit messages (e.g. `add: order PDF export`,
`fix: typesense import command`, `refactor: extract payment service`).

## License

By contributing, you agree that your contributions will be licensed under the
project's [MIT License](LICENSE).
