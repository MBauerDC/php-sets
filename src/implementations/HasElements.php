<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Psalm\Pure;

trait HasElements
{
    protected array $elements;

    /**
     * @return string[]
     */
    #[Pure]
    public function getElementIds(): array
    {
        $thisObjectId = spl_object_id($this);
        static $memo;
        if (null === $memo) {
            $memo = [];
        }
        if (!\array_key_exists($thisObjectId, $memo)) {
            $keys = \array_keys($this->elements);
            $memo[$thisObjectId] = \array_map(static fn(mixed $key): string => (string)$key, $keys);
        }
        return $memo[$thisObjectId];

    }
}