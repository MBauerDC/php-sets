<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Immutable;
use Psalm\Pure;

/**
 * Implementations whose data are entities managed by object-id (e.g. Doctrine ORM)
 * must copy those as references on calls to clone (and SHOULD use a WeakRef).
 * Other implementations should properly clone throughout the type-hierarchy of objects received as data.
 *
 * @template T as mixed
 */
#[Immutable]
interface TypedElement extends Element
{
    /**
     * @return class-string<T>
     */
    #[Pure]
    public function getType(): string;

    /**
     * @return TypedElement<T>
     */
    #[Pure]
    public function clone(): TypedElement;

    #[Pure]
    public function cloneAsElement(): Element;
}