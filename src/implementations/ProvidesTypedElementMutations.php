<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use InvalidArgumentException;
use MBauer\PhpSets\contracts\TypedElement;
use function array_key_exists;

/**
 * @template T of mixed
 */
trait ProvidesTypedElementMutations
{
    use HasTypeString, HasMutableElements;

    /**
     * @param TypedElement<T> ...$els
     * @return void
     */
    public function addElements(TypedElement ...$els): void
    {
        foreach ($els as $el) {
            if ($this->type !== $el->getType()) {
                throw new InvalidArgumentException('Can only add Elements of same Type [' . $this->type . ']');
            }
            $this->elements[$el->getIdentifier()] = $el;
        }
    }
    /**
     * @param TypedElement<T> ...$els
     * @return void
     */
    public function removeElements(TypedElement ...$els): void
    {
        foreach ($els as $el) {
            $id = $el->getIdentifier();
            if (array_key_exists($id, $this->elements)) {
                unset($this->elements[$id]);
            }
        }
    }

    public function removeElementsById(string ...$ids): void
    {
        foreach ($ids as $id) {
            if (array_key_exists($id, $this->elements)) {
                unset($this->elements[$id]);
            }
        }
    }
}
