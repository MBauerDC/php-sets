<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Immutable;
use Psalm\Pure;

/**
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