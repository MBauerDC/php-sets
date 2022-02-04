<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Mbauer\PhpSets\contracts\Element;
use function is_object;

class GenericElement implements Element
{   
    public readonly $id;
    public readonly mixed $data;

    public function __construct(mixed $data, string $id)
    {
        $this->data = $data;
        $this->id = $id;
    }

    public function getIdentifier(): string
    {
        return $this->id;
    }

    public function clone(): Element
    {

        if (is_object($this->data)) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new static($clonedData, $this->id);
    }
}