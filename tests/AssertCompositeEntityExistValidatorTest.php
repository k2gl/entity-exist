<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist\Test;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Generator;
use K2gl\Component\Validator\Constraint\EntityExist\AssertCompositeEntityExist;
use K2gl\Component\Validator\Constraint\EntityExist\AssertCompositeEntityExistValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class AssertCompositeEntityExistValidatorTest extends TestCase
{
    private MockObject $entityManager;

    private MockObject $context;

    private MockObject $repository;

    private AssertCompositeEntityExistValidator $assertCompositeEntityExistValidator;

    protected function setUp(): void
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->context       = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $this->repository    = $this->getMockBuilder(EntityRepository::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->assertCompositeEntityExistValidator = new AssertCompositeEntityExistValidator($this->entityManager);
        $this->assertCompositeEntityExistValidator->initialize($this->context);
    }

    public function testValidateWithWrongConstraint(): void
    {
        // assert
        $this->expectException(UnexpectedTypeException::class);

        // act
        $this->assertCompositeEntityExistValidator->validate(new class () {}, new NotNull);
    }

    public function testValidateWithNonObjectValue(): void
    {
        // assert
        $this->expectException(UnexpectedValueException::class);

        // act
        $this->assertCompositeEntityExistValidator->validate(
            'foo',
            new AssertCompositeEntityExist(entity: 'App\Entity\WarehouseItem', fields: ['warehouseId', 'companyId']),
        );
    }

    public function testValidateSkipsIfValueNull(): void
    {
        // arrange
        $constraint = new AssertCompositeEntityExist(entity: 'App\Entity\WarehouseItem', fields: ['warehouseId', 'companyId']);

        // assert
        $this->context->expects(self::never())->method('buildViolation');
        $this->entityManager->expects(self::never())->method('getRepository');

        // act
        $this->assertCompositeEntityExistValidator->validate(null, $constraint);
    }

    #[DataProvider('getEmptyOrNull')]
    public function testValidateSkipsIfAnyFieldEmptyOrNull(mixed $companyId): void
    {
        // arrange
        $order      = new MoveStockOrder(warehouseId: 'w-1', companyId: $companyId);
        $constraint = new AssertCompositeEntityExist(entity: 'App\Entity\WarehouseItem', fields: ['warehouseId', 'companyId']);

        // assert
        $this->context->expects(self::never())->method('buildViolation');
        $this->entityManager->expects(self::never())->method('getRepository');

        // act
        $this->assertCompositeEntityExistValidator->validate($order, $constraint);
    }

    public function testValidateValidCombination(): void
    {
        // arrange
        $item       = new class () {};
        $order      = new MoveStockOrder(warehouseId: 'w-1', companyId: 'c-1');
        $constraint = new AssertCompositeEntityExist(entity: 'App\Entity\WarehouseItem', fields: ['warehouseId', 'companyId']);

        // assert
        $this->context->expects(self::never())->method('buildViolation');

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['warehouseId' => 'w-1', 'companyId' => 'c-1'])
            ->willReturn($item);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with('App\Entity\WarehouseItem')
            ->willReturn($this->repository);

        // act
        $this->assertCompositeEntityExistValidator->validate($order, $constraint);
    }

    public function testValidateInvalidCombinationBuildsViolationAtFirstFieldByDefault(): void
    {
        // arrange
        $order      = new MoveStockOrder(warehouseId: 'w-1', companyId: 'c-1');
        $constraint = new AssertCompositeEntityExist(entity: 'App\Entity\WarehouseItem', fields: ['warehouseId', 'companyId']);

        // assert
        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violationBuilder->method('setParameter')->willReturnSelf();
        $violationBuilder->expects(self::once())->method('atPath')->with('warehouseId')->willReturnSelf();

        $this->context->expects(self::once())->method('buildViolation')->willReturn($violationBuilder);

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->willReturn($this->repository);

        // act
        $this->assertCompositeEntityExistValidator->validate($order, $constraint);
    }

    public function testValidateInvalidCombinationUsesCustomErrorPath(): void
    {
        // arrange
        $order      = new MoveStockOrder(warehouseId: 'w-1', companyId: 'c-1');
        $constraint = new AssertCompositeEntityExist(
            entity: 'App\Entity\WarehouseItem',
            fields: ['warehouseId', 'companyId'],
            errorPath: 'companyId',
        );

        // assert
        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violationBuilder->method('setParameter')->willReturnSelf();
        $violationBuilder->expects(self::once())->method('atPath')->with('companyId')->willReturnSelf();

        $this->context->expects(self::once())->method('buildViolation')->willReturn($violationBuilder);

        $this->repository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->willReturn($this->repository);

        // act
        $this->assertCompositeEntityExistValidator->validate($order, $constraint);
    }

    public static function getEmptyOrNull(): Generator
    {
        yield [''];
        yield [null];
    }
}

readonly class MoveStockOrder
{
    public function __construct(
        public string $warehouseId,
        public mixed $companyId,
    ) {}
}
