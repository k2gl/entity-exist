<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class AssertEntityNotExist extends Constraint
{
    public const EXIST = 'f7b817e0-4536-46e2-b3dd-2fdbbbdb9031';

    protected const ERROR_NAMES = [
        self::EXIST => 'EXIST',
    ];

    #[HasNamedArguments]
    public function __construct(
        public string $entity,
        public string $property = 'id',
        public string $message = 'Entity "%entity%" with property "%property%": "%value%" already exist.',
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}