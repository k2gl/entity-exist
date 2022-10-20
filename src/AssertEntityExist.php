<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class AssertEntityExist extends Constraint
{
    public const NOT_EXIST = '17da91df-d2f6-4c73-a90c-e10712efd5ff';

    protected const ERROR_NAMES = [
        self::NOT_EXIST => 'NOT_EXIST',
    ];

    #[HasNamedArguments]
    public function __construct(
        public string $entity,
        public string $property = 'id',
        public string $message = 'Entity "%entity%" with property "%property%": "%value%" does not exist.',
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}