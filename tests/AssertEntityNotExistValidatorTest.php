<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist\Test;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Generator;
use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityNotExist;
use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityNotExistValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class AssertEntityNotExistValidatorTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $context;
    private MockObject $repository;
    private AssertEntityNotExistValidator $validator;

    protected function setUp(): void
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->context       = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $this->repository    = $this->getMockBuilder(EntityRepository::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->validator = new AssertEntityNotExistValidator($this->entityManager);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithWrongConstraint(): void
    {
        // assert
        $this->expectException(UnexpectedTypeException::class);

        // act
        $this->validator->validate('foo', new NotNull());
    }

    public function testValidateValidEntity(): void
    {
        // arrange
        $constraint = new AssertEntityNotExist(entity: 'App\Entity\User');

        // assert
        $this->context->expects(self::never())->method('buildViolation');

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 'foo'])
            ->willReturn(null);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with('App\Entity\User')
            ->willReturn($this->repository);

        // act
        $this->validator->validate('foo', $constraint);
    }

    /** @dataProvider getEmptyOrNull */
    public function testValidateSkipsIfValueEmptyOrNull($value): void
    {
        // arrange
        $constraint = new AssertEntityNotExist(entity: 'App\Entity\User');

        // assert
        $this->context->expects(self::never())->method('buildViolation');

        $this->repository
            ->expects($this->exactly(0))
            ->method('findOneBy')
            ->with(['id' => $value])
            ->willReturn(null);

        $this->entityManager
            ->expects($this->exactly(0))
            ->method('getRepository')
            ->with('App\Entity\User')
            ->willReturn($this->repository);

        // act
        $this->validator->validate($value, $constraint);
    }

    public function testValidateValidEntityWithCustomProperty(): void
    {
        // arrange
        $constraint = new AssertEntityNotExist(entity: 'App\Entity\User', property: 'uuid');

        // assert
        $this->context->expects(self::never())->method('buildViolation');

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['uuid' => 'foo'])
            ->willReturn(null);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with('App\Entity\User')
            ->willReturn($this->repository);

        // act
        $this->validator->validate('foo', $constraint);
    }

    public function testValidateInvalidEntity(): void
    {
        // arrange
        $constraint = new AssertEntityNotExist(entity: 'App\Entity\User');

        // assert
        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violationBuilder->method('setParameter')->will($this->returnSelf());

        $this->context->expects(self::once())->method('buildViolation')->willReturn($violationBuilder);

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn('user');

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->willReturn($this->repository);

        // act
        $this->validator->validate('foo', $constraint);
    }

    public function getEmptyOrNull(): Generator
    {
        yield [''];
        yield [null];
    }
}
