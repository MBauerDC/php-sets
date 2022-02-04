<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

interface TypedElement extends Element
{   
    public function getType(): string;

    public function clone(): TypedElement;

    public function cloneAsElement(): Element;
}