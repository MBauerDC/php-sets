<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\MutableTypedSet;

/**
 * @template T of mixed
 */
class GenericMutableTypedSet extends GenericTypedSet implements MutableTypedSet
{
    use ProvidesTypedElementMutations;
}