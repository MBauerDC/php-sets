<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use MBauer\PhpSets\contracts\TypedElement;

interface HasTypedElement
{
    public function hasElement(TypedElement $el): bool;
}