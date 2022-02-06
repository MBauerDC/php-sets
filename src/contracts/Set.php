<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Immutable;
use Psalm\Pure;

#[Immutable]
interface Set extends  BaseSet, HasElement, HasElementById
{

    #[Pure]
    public function isSubsetOf(Set $set): bool;

    #[Pure]
    public function without(Set ...$sets): Set;

    #[Pure]
    public function intersectWith(Set ...$sets): Set;

    #[Pure]
    public function unionWith(Set ...$sets): Set;

    #[Pure]
    public function symmetricDifferenceWith(Set ...$sets): Set;
    
    /**
     * @param pure-callable(Element):bool $filterFn
     */
    #[Pure]
    public function filter(callable $filterFn): Set;

    #[Pure]
    public function clone(): Set;
}

