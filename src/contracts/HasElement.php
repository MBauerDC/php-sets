<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Pure;

interface HasElement
{
    #[Pure]
    public function hasElement(Element $el): bool;
}