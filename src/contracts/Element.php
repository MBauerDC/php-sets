<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;


use Psalm\Immutable;
use Psalm\Pure;

/**
 * Implementations whose data are entities managed by object-id (e.g. Doctrine ORM)
 * must copy those as references on calls to clone (and SHOULD use a WeakRef).
 * Other implementations should properly clone throughout the type-hierarchy of objects received as data.
 */
#[Immutable]
interface Element
{   /** 
    * @return string Identifier - can be id, hash etc
    */
    #[Pure]
    public function getIdentifier(): string;

    #[Pure]
    public function getData(): mixed;

    #[Pure]
    public function clone(): Element;
}