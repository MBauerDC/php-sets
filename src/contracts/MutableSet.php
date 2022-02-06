<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

interface MutableSet extends Set
{
    public function addElements(Element ...$els): void;
    public function removeElements(Element ...$els): void;
    public function removeElementsById(string ...$ids): void;
}