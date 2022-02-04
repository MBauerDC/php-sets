<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

/**
 * @template T
 */
interface TypedSet extends BaseSet, HasTypedElement, HasElementById
{   
    /**
     * @return TypedElement<T>[]
     */
    public function toArray(): array;

    public function getType(): string;

    /**
     * @return TypedElement<T>|null
     */
    public function getElementById(string $id): ?TypedElement;

    /**
     * @return TypedSet<T>
     */
    public function clone(): TypedSet;

    public function cloneAsSet(): Set;

    /**
     * @param TypedSet<T> $sets
     */
    public function withoutSet(TypedSet ...$sets): bool;

    /**
     * @param TypedSet<T> $sets
     */
    public function isSubsetOf(TypedSet ...$sets): bool;

    /**
     * @param TypedSet<T> $sets
     * @return TypedSet<T>
     */
    public function intersectWith(TypedSet ...$sets): TypedSet;

    /**
     * @param TypedSet<T> $sets
     * @return TypedSet<T>
     */
    public function unionWith(TypedSet ...$sets): TypedSet;
    
    /**
     * @param TypedSet<T> $sets
     * @return TypedSet<T>
     */
    public function without(TypedSet ...$sets): TypedSet;

    /**
     * @param TypedSet<T> $sets
     * @return TypedSet<T>
     */
    public function symmetricDifferenceWith(TypedSet ...$set): TypedSet;
    
    /**
     * @param callable(TypedElement<T>):bool
     * @return TypedSet<T>
     */
    public function filter(callable $filterFn): TypedSet;

}