<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Element;
use Psalm\Immutable;
use Psalm\Pure;
use function is_object;

#[Immutable]
class GenericElement implements Element
{   
    public readonly string $id;
    public readonly mixed $data;

    public function __construct(mixed $data, string $id)
    {
        $this->data = $data;
        $this->id = $id;
    }

    #[Pure]
    public function getIdentifier(): string
    {
        return $this->id;
    }

    #[Pure]
    public function clone(): Element
    {

        if (is_object($this->data)) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new self($clonedData, $this->id);
    }
}