<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AssertEntityNotExistValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssertEntityNotExist) {
            throw new UnexpectedTypeException($constraint, expectedType: AssertEntityNotExist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $data = $this->entityManager->getRepository($constraint->entity)->findOneBy([
            $constraint->property => $value,
        ]);

        if (null !== $data) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('%entity%', $constraint->entity)
                          ->setParameter('%property%', $constraint->property)
                          ->setParameter('%value%', (string)$value)
                          ->setCode(AssertEntityNotExist::EXIST)
                          ->addViolation();
        }
    }
}