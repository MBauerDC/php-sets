<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Countable;
use IteratorAggregate;

interface BaseSet extends IteratorAggregate, Countable
{
    public function toArray(): array;

    /**
     * @return string[]
     */
    public function getElementIds(): array;

    public function getElementById(string $id): ?Element;

}