<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist\Test;

use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityExist;
use K2gl\Component\Validator\Constraint\EntityExist\AssertEntityNotExist;
use K2gl\Component\Validator\Constraint\EntityExist\ViolationCode;
use PHPUnit\Framework\TestCase;

/**
 * Guards that the readability facade does not drift from the constraint
 * constants that own the actual UUID values.
 */
class ViolationCodeTest extends TestCase
{
    public function testNotExistMatchesAssertEntityExist(): void
    {
        self::assertSame(AssertEntityExist::NOT_EXIST, ViolationCode::NOT_EXIST);
    }

    public function testAlreadyExistMatchesAssertEntityNotExist(): void
    {
        self::assertSame(AssertEntityNotExist::EXIST, ViolationCode::ALREADY_EXIST);
    }
}
