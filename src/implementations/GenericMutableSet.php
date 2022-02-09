<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\MutableSet;

/**
 * @template T of mixed
 */
class GenericMutableSet extends GenericSet implements MutableSet
{
    use ProvidesElementMutations;
}