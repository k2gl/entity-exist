# Assert entity exist or not using Symfony constraints and Doctrine.

[![GitHub Actions](https://github.com/k2gl/entity-exist/workflows/CI/badge.svg)](https://github.com/k2gl/entity-exist/actions?workflow=CI)

## Requirements

- PHP 8.1+
- Symfony 6.1, 7.x or 8.x (`symfony/validator`, `symfony/dependency-injection`)
- Doctrine ORM 2.13+ or 3.x

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require k2gl/entity-exist
```

## Configuration

Makes classes in src/ available to be used as services in **services.yaml**

```
services:
    K2gl\Component\Validator\Constraint\EntityExist\:
        resource: '../vendor/k2gl/entity-exist/src/'
        arguments: ['@doctrine.orm.entity_manager']
        tags:
            - { name: validator.constraint_validator }
```

## Usage

### AssertEntityNotExist

```php
use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityNotExist;

readonly class RegisterUserOrder
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[AssertEntityNotExist(
            entity: User::class,
            property: 'email',
            message: 'User with email "%value%" already registered.'
        )]
        public string $email,
    ) {
    }
}
```

### AssertEntityExist

```php
use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityExist;

readonly class TransferUserToOtherUnitOrder
{
    public function __construct(
        #[Assert\NotBlank]
        #[AssertEntityExist(
            entity: User::class,
            property: 'uuid',
        )]
        public string $user,
        #[Assert\NotBlank]
        #[AssertEntityExist(
            entity: Unit::class,
            property: 'uuid',
        )]
        public string $unit,        
    ) {
    }
}
```

## Violation codes

Each constraint declares the violation code it emits as a UUID constant on its
own class (`AssertEntityExist::NOT_EXIST`, `AssertEntityNotExist::EXIST`).
Reading those at the call site can be awkward — especially
`AssertEntityNotExist::EXIST`, where the class name and the constant negate
each other.

For nicer reading in error handling and tests, the same codes are also exposed
under neutral names on `ViolationCode`:

```php
use K2gl\Component\Validator\Constraint\EntityExist\ViolationCode;

foreach ($validator->validate($dto) as $violation) {
    if ($violation->getCode() === ViolationCode::ALREADY_EXIST) {
        // handle "entity already exists" case
    }

    if ($violation->getCode() === ViolationCode::NOT_EXIST) {
        // handle "entity not found" case
    }
}
```

`ViolationCode` constants are plain strings that reference the constraint
constants — no duplication, no separate source of truth.

## Pull requests are always welcome
[Collaborate with pull requests](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request)

