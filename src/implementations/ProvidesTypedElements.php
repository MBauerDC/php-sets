<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\TypedElement;
use Psalm\Pure;

/**
 * @template T of mixed
 */
trait ProvidesTypedElements
{
    use HasElements;


    /**
     * @param string $id
     * @return TypedElement<T>|null
     */
    #[Pure]
    public function getElementById(string $id): ?TypedElement
    {
        return $this->elements[$id] ?? null;
    }

}