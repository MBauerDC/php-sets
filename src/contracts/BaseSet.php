<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psalm\Pure;

/**
 * @extends  IteratorAggregate<string,Element>
 */
interface BaseSet extends IteratorAggregate, Countable
{

    /**
     * @return ArrayIterator<string,Element>
     */
    public function getIterator(): ArrayIterator;

    /**
     * @return array<string, Element>
     */
    #[Pure]
    public function toArray(): array;

    /**
     * @return string[]
     */
    #[Pure]
    public function getElementIds(): array;

    #[Pure]
    public function getElementById(string $id): ?Element;

}