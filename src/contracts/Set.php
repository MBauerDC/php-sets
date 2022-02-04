<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;


interface Set extends  BaseSet, HasElement, HasElementById
{

    public function isSubsetOf(Set $set): bool;

    public function withoutSets(Set ...$sets): Set;

    public function intersect(Set ...$sets): Set;

    public function unionWith(Set ...$sets): Set;
    
    public function symmetricDifferenceWith(Set ...$sets): Set;
    
    /**
     * @param callable(Element):bool
     */
    public function filter(callable $filterFn): Set;
}

