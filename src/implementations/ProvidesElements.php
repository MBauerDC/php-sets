<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Element;
use Psalm\Pure;

trait ProvidesElements
{
    use HasElements;

    #[Pure]
    public function getElementById(string $id): ?Element
    {
        return $this->elements[$id] ?? null;
    }

}