<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

use Psalm\Pure;

interface HasElementById
{
    #[Pure]
    public function hasElementById(string $id): bool;
}