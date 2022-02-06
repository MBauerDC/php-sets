<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use ArrayIterator;
use MBauer\PhpSets\contracts\BaseSet;
use MBauer\PhpSets\contracts\Element;
use Psalm\Pure;

abstract class GenericBaseSet implements BaseSet
{
    use HasElements;

    public function __construct(Element ...$els)
    {
        foreach ($els as $el) {
            $id = $el->getIdentifier();
            $this->elements[$id] = $el;
        }
    }

    /**
     * @return array<string, Element>
     */
    #[Pure]
    public function toArray(): array
    {
        $clones = [];
        foreach ($this->elements as $id => $el) {
            $clones[$id] = $el->clone();
        }
        return $clones;
    }

    /**
     * @return ArrayIterator<string, Element>
     */
    #[Pure]
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }

}