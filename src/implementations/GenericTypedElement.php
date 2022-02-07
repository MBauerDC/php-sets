<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use InvalidArgumentException;
use MBauer\PhpSets\contracts\Element;
use MBauer\PhpSets\contracts\TypedElement;
use Psalm\Immutable;
use Psalm\Pure;
use ReflectionClass;
use WeakReference;
use function is_object;

/**
 * @template T as mixed
 * @implements TypedElement<T>
 */
#[Immutable]
class GenericTypedElement implements TypedElement
{
    use HasTypeString;

    public readonly string $id;
    public readonly mixed $data;
    protected bool $cloneData = false;

    /**
     * @param class-string<T> $type
     * @param mixed $data
     * @param string $id
     */
    public function __construct(string $type, mixed $data, string $id, ElementBehavior $behavior = ElementBehavior::DATA_COPY_ASSIGNMENT)
    {
        $this->type = $type;
        $this->id = $id;

        switch ($behavior) {
            case ElementBehavior::DATA_COPY_ASSIGNMENT:
                $this->data = $data;
                break;
            case ElementBehavior::DATA_CLONE_WHERE_POSSIBLE:
                $clone = false;
                if (is_object($data)) {
                    $reflClass = new ReflectionClass($data);
                    $clone = $reflClass->isCloneable();
                    $this->cloneData = true;
                }
                if ($clone) {
                    $this->data = clone $data;
                } else {
                    $this->data = $data;
                }
                break;
            case ElementBehavior::DATA_COPY_WITH_WEAKREF:
                if (is_object($data)) {
                    $this->data = WeakReference::create($data);
                } else {
                    $this->data = $data;
                }
                break;
            default:
                throw new InvalidArgumentException(
                    'Unknown behavior specified. See enum ElementBehavior for valid options.'
                );
        }
    }

    #[Pure]
    public function getIdentifier(): string
    {
        return $this->id;
    }

    #[Pure]
    public function getData(): mixed
    {
        return $this->data instanceof WeakReference ? $this->data->get() : $this->data;
    }

    /**
     * @return TypedElement<T>
     */
    #[Pure]
    public function clone(): TypedElement
    {
        if ($this->cloneData) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new self($this->type, $clonedData, $this->id);
    }

    #[Pure]
    public function cloneAsElement(): Element
    {
        if ($this->cloneData) {
            $clonedData = clone $this->data;
        } else {
            $clonedData = $this->data;
        }
        return new GenericElement($clonedData, $this->id);
    }
}