<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\TypedElement;
use Psalm\Pure;

/**
 * @template T of mixed
 */
trait ProvidesTypedElementCheck
{
    use HasElements, HasTypeString;

    /**
     * @param TypedElement<T> $el
     * @return bool
     */
    #[Pure]
    public function hasElement(TypedElement $el): bool
    {
        if ($el->getType() !== $this->type) {
            return false;
        }
        return $this->hasElementById($el->getIdentifier());
    }

    #[Pure]
    public function hasElementById(string $id): bool
    {
        return array_key_exists($id, $this->elements);
    }
}