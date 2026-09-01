# Contributing

Contributions are welcome and will be fully credited.

## Pull Requests

- **Follow the coding style** — run `composer format` (Laravel Pint) before committing.
- **Add tests** for any behavior you fix or add, and make sure `composer test` passes.
- **Static analysis** — run `composer analyse` (Larastan/PHPStan) and resolve any new findings.
- **Document changes** — update the `README.md` and `CHANGELOG.md` when behavior changes.
- **One feature per PR** — send one pull request per fix or feature so it can be reviewed and merged independently.

## Running Tests

```bash
composer install
composer test
```
