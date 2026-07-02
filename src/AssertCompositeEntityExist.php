<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
final class AssertCompositeEntityExist extends Constraint
{
    public const NOT_EXIST = '1b28e17b-f73d-4be9-900c-a35a8f9e7690';

    protected const ERROR_NAMES = [
        self::NOT_EXIST => 'NOT_EXIST',
    ];

    /**
     * @param class-string $entity
     * @param non-empty-list<string> $fields
     */
    #[HasNamedArguments]
    public function __construct(
        public string $entity,
        public array $fields,
        public string $message = 'Entity "%entity%" with fields "%fields%": "%values%" does not exist.',
        public ?string $errorPath = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
