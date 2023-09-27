# Assert entity exist or not using Symfony constraints and Doctrine.

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

## Pull requests are always welcome
[Collaborate with pull requests](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request)

