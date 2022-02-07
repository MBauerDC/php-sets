<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use InvalidArgumentException;
use MBauer\PhpSets\contracts\Element;
use Psalm\Immutable;
use Psalm\Pure;
use ReflectionClass;
use ReflectionException;
use WeakReference;
use function is_object;

#[Immutable]
class GenericElement implements Element
{

    public readonly string $id;
    public readonly mixed $data;
    protected bool $cloneData = false;

    /**
     * @param mixed $data
     * @param string $id
     * @param ElementBehavior $behavior
     * @throws ReflectionException
     */
    public function __construct(mixed $data, string $id, ElementBehavior $behavior = ElementBehavior::DATA_COPY_ASSIGNMENT)
    {
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
        $this->id = $id;
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

    #[Pure]
    public function clone(): Element
    {
        $clonedData = $this->cloneData ? clone $this->data : $this->data;
        return new self($clonedData, $this->id);
    }
}