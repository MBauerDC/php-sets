<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Psalm\Pure;

trait HasElements
{
    protected array $elements = [];

    /**
     * @return string[]
     */
    #[Pure]
    public function getElementIds(): array
    {
        return array_keys($this->elements);
    }
}