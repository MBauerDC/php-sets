<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

/**
 * @template T of mixed
 */
interface MutableTypedSet extends TypedSet
{
    /**
     * @param TypedElement<T> ...$els
     * @return void
     */
    public function addElements(TypedElement ...$els): void;

    /**
     * @param TypedElement<T> ...$els
     * @return void
     */
    public function removeElements(TypedElement ...$els): void;

    public function removeElementsById(string ...$ids): void;
}