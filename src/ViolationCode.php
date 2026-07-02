<?php

declare(strict_types=1);

namespace K2gl\Component\Validator\Constraint\EntityExist;

/**
 * Human-readable reference for the violation codes emitted by the
 * AssertEntityExist, AssertEntityNotExist and AssertCompositeEntityExist constraints.
 *
 * The constraints themselves remain the source of truth for the actual UUID
 * values; this class only re-exposes them under names that read well at the
 * call site. Typical use — branching on a violation code:
 *
 *     foreach ($violations as $violation) {
 *         if ($violation->getCode() === ViolationCode::ALREADY_EXIST) {
 *             // handle "entity already exists" case
 *         }
 *     }
 */
final class ViolationCode
{
    /** Entity required to exist was not found. */
    public const NOT_EXIST = AssertEntityExist::NOT_EXIST;

    /** Entity required to be absent already exists. */
    public const ALREADY_EXIST = AssertEntityNotExist::EXIST;

    /** No row matches the combination of fields required to exist together. */
    public const COMPOSITE_NOT_EXIST = AssertCompositeEntityExist::NOT_EXIST;

    private function __construct() {}
}
