# Changelog

## 1.14.0

- Add `ViolationCode` — a small reference class re-exposing the violation codes
  declared by `AssertEntityExist` and `AssertEntityNotExist` under neutral names
  (`NOT_EXIST`, `ALREADY_EXIST`), suitable for error handling and tests.
  Fully backward compatible; no constants removed or renamed.

## 1.13.0

- Add support for Symfony 8 (`symfony/validator` and `symfony/dependency-injection` `^8.0`).
- Make both constraints clean on PHP 8.4: explicit nullable `?array $groups`, and
  `parent::__construct(null, ...)` instead of passing an (now ignored) options array.
- CI now runs a matrix across PHP 8.1–8.4 and Symfony 6.4/7/8 with Doctrine ORM 2 and 3, plus a
  static-analysis job (PHPStan, PHP-CS-Fixer). Test runs fail on any deprecation coming from this
  package's own code.
- Dev tooling refreshed: PHPUnit `^10.5|^11|^12`, Rector `^2`, add PHPStan and PHP-CS-Fixer.

No behavioural or API changes; fully backward compatible.
