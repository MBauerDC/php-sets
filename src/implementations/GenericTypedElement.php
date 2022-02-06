<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Element;
use MBauer\PhpSets\contracts\TypedElement;
use Psalm\Immutable;
use Psalm\Pure;
use function is_object;

/**
 * @template T as mixed
 * @implements TypedElement<T>
 */
#[Immutable]
class GenericTypedElement implements TypedElement
{
    public readonly string $id;
    /**
     * @var class-string<T>
     */
    public readonly string $type;
    public readonly mixed $data;

    /**
     * @psalm-param class-string<T> $type
     * @psalm-param mixed $data
     * @psalm-param string $id
     */
    public function __construct(string $type, mixed $data, string $id)
    {
        $this->type = $type;
        $this->data = $data;
        $this->id = $id;
    }

    #[Pure]
    public function getIdentifier(): string
    {
        return $this->id;
    }

    /**
     * @return class-string<T>
     */
    #[Pure]
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return TypedElement<T>
     */
    #[Pure]
    public function clone(): TypedElement
    {
        if (is_object($this->data)) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new self($this->type, $clonedData, $this->id);
    }

    #[Pure]
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