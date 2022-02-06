<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;


use Psalm\Immutable;
use Psalm\Pure;

#[Immutable]
interface Element
{   /** 
    * @return string Identifier - can be id, hash etc
    */
    #[Pure]
    public function getIdentifier(): string;

    #[Pure]
    public function clone(): Element;
}