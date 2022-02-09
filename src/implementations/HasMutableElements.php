<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Psalm\Pure;

trait HasMutableElements
{
    use HasElements;

    /**
     * @return string[]
     */
    #[Pure]
    public function getElementIds(): array
    {
        $keys = \array_keys($this->elements);
        $asStrings = \array_map(static fn(mixed $key): string => (string)$key, $keys);
        return $asStrings;
    }
}