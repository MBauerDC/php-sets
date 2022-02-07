<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Psalm\Pure;

/**
 * @template T of mixed
 */
trait HasTypeString
{

    public readonly string $type;

    /**
     * @return class-string<T>
     */
    #[Pure]
    public function getType(): string
    {
        return $this->type;
    }

}