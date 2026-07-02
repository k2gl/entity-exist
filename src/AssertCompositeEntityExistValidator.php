<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

use Doctrine\ORM\EntityManagerInterface;
use Stringable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class AssertCompositeEntityExistValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof AssertCompositeEntityExist) {
            throw new UnexpectedTypeException($constraint, expectedType: AssertCompositeEntityExist::class);
        }

        if (null === $value) {
            return;
        }

        if (! is_object($value)) {
            throw new UnexpectedValueException($value, expectedType: 'object');
        }

        $criteria = [];

        foreach ($constraint->fields as $field) {
            if (! property_exists($value, $field)) {
                throw new UnexpectedValueException($value, expectedType: sprintf('object with property "%s"', $field));
            }

            $fieldValue = $value->$field;

            if (null === $fieldValue || '' === $fieldValue) {
                return;
            }

            $criteria[$field] = $fieldValue;
        }

        $data = $this->entityManager->getRepository($constraint->entity)->findOneBy($criteria);

        if (null === $data) {
            $this->context->buildViolation($constraint->message)
                          ->atPath($constraint->errorPath ?? $constraint->fields[0])
                          ->setParameter('%entity%', $constraint->entity)
                          ->setParameter('%fields%', implode(', ', $constraint->fields))
                          ->setParameter('%values%', implode(', ', array_map(
                              static fn (mixed $criterionValue): string => is_scalar($criterionValue) || $criterionValue instanceof Stringable
                                  ? (string) $criterionValue
                                  : get_debug_type($criterionValue),
                              $criteria,
                          )))
                          ->setCode(AssertCompositeEntityExist::NOT_EXIST)
                          ->addViolation();
        }
    }
}
