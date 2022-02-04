<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implemenetations;

use Mbauer\PhpSets\contracts\Element;
use Mbauer\PhpSets\contracts\TypedElement;
use function is_object;


class GenericTypedElement implements TypedElement
{   
    public readonly string $id;
    public readonly string $type;
    public readonly mixed $data;

    public function __construct(string $type, mixed $data, string $id)
    {
        $this->type = $type;
        $this->data = $data;
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function clone(): TypedElement
    {
        if (is_object($this->data)) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new static($this->type, $clonedData, $this->id);
    }

    public function cloneAsElement(): Element
    {
        if (is_object($this->data)) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new GenericElement($clonedData, $this->id);
    }
}