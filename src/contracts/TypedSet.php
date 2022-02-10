<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;


use ArrayIterator;
use Psalm\Immutable;
use Psalm\Pure;

/**
 * @template T as mixed
 * @extends HasTypedElement<T>
 * @extends TypedElement<T>
 */
#[Immutable]
interface TypedSet extends BaseSet, TypedElement, HasTypedElement, HasElementById
{

    /**
     * @return array<string, TypedElement<T>>
     */
    public function getData(): array;

    /**
     * @return array<string, TypedElement<T>>
     */
    #[Pure]
    public function toArray(): array;

    /**
     * @return class-string<T>
     */
    #[Pure]
    public function getType(): string;

    /**
     * @param string $id
     * @return TypedElement<T>|null
     */
    #[Pure]
    public function getElementById(string $id): ?TypedElement;

    /**
     * @return TypedSet<T>
     */
    #[Pure]
    public function clone(): TypedSet;

    /**
     * @return Set
     */
    #[Pure]
    public function cloneAsSet(): Set;

    /**
     * @param TypedSet<T> $set
     */
    #[Pure]
    public function isSubsetOf(TypedSet $set): bool;

    /**
     * @param TypedSet<T> ...$sets
     * @return TypedSet<T>
     */
    #[Pure]
    public function intersectWith(TypedSet ...$sets): TypedSet;

    /**
     * @param TypedSet<T> ...$sets
     * @return TypedSet<T>
     */
    #[Pure]
    public function unionWith(TypedSet ...$sets): TypedSet;
    
    /**
     * @param TypedSet<T> ...$sets
     * @return TypedSet<T>
     */
    #[Pure]
    public function without(TypedSet ...$sets): TypedSet;

    /**
     * @param TypedSet<T> ...$sets
     * @return TypedSet<T>
     */
    #[Pure]
    public function symmetricDifferenceWith(TypedSet ...$sets): TypedSet;
    
    /**
     * @param callable(TypedElement<T>):bool $filterFn
     * @return TypedSet<T>
     */
    #[Pure]
    public function filter(callable $filterFn): TypedSet;

    /**
     * @return ArrayIterator<string,TypedElement<T>>
     */
    #[Pure]
    public function getIterator(): ArrayIterator;

    #[Pure]
    public function getPowerSet(): TypedSet;

}