<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Pure;

/**
 * @template T as mixed
 */
interface HasTypedElement
{
    /**
     * @param TypedElement<T> $el
     * @return bool
     */
    #[Pure]
    public function hasElement(TypedElement $el): bool;
}