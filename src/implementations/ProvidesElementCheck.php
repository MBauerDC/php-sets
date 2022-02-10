<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Element;
use Psalm\Pure;
use function array_key_exists;

trait ProvidesElementCheck
{
    use HasElements;

    #[Pure]
    public function hasElement(Element $el): bool
    {
        return $this->hasElementById($el->getIdentifier());
    }

    #[Pure]
    public function hasElementById(string $id): bool
    {
        return array_key_exists($id, $this->elements);
    }
}