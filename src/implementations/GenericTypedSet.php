<?php
declare(strict_types=1);


namespace MBauer\PhpSets\implementations;

use ArrayIterator;
use InvalidArgumentException;
use MBauer\PhpSets\contracts\Set;
use MBauer\PhpSets\contracts\TypedElement;
use MBauer\PhpSets\contracts\TypedSet;
use Psalm\Immutable;
use Psalm\Pure;
use function array_diff_key;
use function array_filter;
use function array_intersect_key;
use function array_map;
use function array_values;
use function count;

/**
 * @template T as mixed
 * @implements TypedSet<T>
 */
#[Immutable]
class GenericTypedSet extends GenericBaseSet implements TypedSet
{
    use HasElements, ProvidesTypedElements, ProvidesTypedElementCheck;

    /**
     * @param class-string<T> $type
     * @param TypedElement<T> ...$els
     */
    public function __construct(string $type, TypedElement ...$els)
    {
        $this->type = $type;
        foreach ($els as $el) {
            $type = $el->getType();
            if ($type !== $this->type) {
                throw new InvalidArgumentException('Can only accept TypedElements with type [' . $this->type . '].');
            }
            $id = $el->getIdentifier();
            $this->elements[$id] = $el;
        }
    }

    /**
     * @return ArrayIterator<string,TypedElement<T>>
     */
    public function getIterator(): ArrayIterator
    {
        return parent::getIterator();
    }

    /**
     * @param TypedSet<T> $set
     * @return bool
     */
    #[Pure]
    public function isSubsetOf(TypedSet $set): bool
    {
        if ($set->getType() !== $this->type) {
            return false;
        }
        $theseIds = $this->getElementIds();
        $theseIds = array_combine($theseIds, $theseIds);
        $thoseIds = $set->getElementIds();
        $thoseIds = array_combine($thoseIds, $thoseIds);
        $thisCount = count($theseIds);
        return count(array_intersect_key($thoseIds, $theseIds)) === $thisCount;
    }


    /**
     * @param TypedSet[] $sets
     * @return TypedSet<T>[]
     */
    protected function filterSetsForSameType(array $sets): array
    {
        $thisType = $this->type;
        $allTypes = array_map(static fn(TypedSet $s) => $s->getType(), $sets);
        $setsCount = count($sets);
        $setsOfSameType = [];
        for ($i = 0; $i < $setsCount; $i++) {
            $currType = $allTypes[$i];
            if ($currType === $thisType) {
                $setsOfSameType[] = $sets[$i];
            }
        }
        return $setsOfSameType;
    }

    /**
     * @inheritdoc
     */
    #[Pure]
    public function without(TypedSet ...$sets): TypedSet
    {
        if (count($sets) === 0) {
            return $this->clone();
        }

        $setsOfSameType = $this->filterSetsForSameType($sets);
        $new = [];
        $theseElements = $this->toArray();
        foreach ($theseElements as $key => $el) {
            $inAnyRHS = false;
            foreach ($setsOfSameType as $set) {
                if ($set->hasElementById($key)) {
                    $inAnyRHS = true;
                    break;
                }
            }
            if (!$inAnyRHS) {
                $new[] = $el;
            }
        }
        return new self($this->type, ...$new);
    }

    /**
     * @inheritdoc
     */
    #[Pure]
    public function intersectWith(TypedSet ...$sets): TypedSet
    {
        $origCount = count($sets);
        if ($origCount === 0) {
            return $this->clone();
        }

        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            return new self($this->type);
        }
        $new = [];
        $theseIds = $this->getElementIds();

        foreach ($theseIds as $idFromThis) {
            $inAll = true;
            foreach ($setsOfSameType as $set) {
                if (!$set->hasElementById($idFromThis)) {
                    $inAll = false;
                    break;
                }
            }
            if ($inAll) {
                $new[] = $this->elements[$idFromThis]->clone();
            }
        }
        return new self($this->type, ...$new);
    }

    /**
     * @inheritdoc
     */
    #[Pure]
    public function unionWith(TypedSet ...$sets): TypedSet
    {
        $origCount = count($sets);
        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            throw new InvalidArgumentException('Can only union with other TypedSets of type [' . $this->type . '].');
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = $otherArr + $currArr;
        }
        return new self($this->type, ...array_values($currArr));
    }

    /**
     * @inheritdoc
     */
    #[Pure]
    public function symmetricDifferenceWith(TypedSet ...$sets): TypedSet
    {
        $origCount = count($sets);
        if ($origCount === 0) {
            return $this->clone();
        }
        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            throw new InvalidArgumentException('Can only create symmetric difference with other TypedSets of type [' . $this->type . '].');
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = array_diff_key($currArr, $otherArr) + array_diff_key($otherArr, $currArr);
        }
        return new self($this->type, ...array_values($currArr));
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
     * @inheritDoc
     */
    #[Pure]
    public function clone(): TypedSet
    {
        $new = [];
        foreach ($this as $el) {
            $new[] = $el->clone();
        }
        return new self($this->type, ...$new);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function cloneAsSet(): Set
    {
        $new = [];
        foreach ($this as $el) {
            $new[] = $el->cloneAsElement();
        }
        return new GenericSet( ...$new);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function filter(callable $filterFn): TypedSet
    {
        $arr = $this->toArray();
        $filteredArr = array_filter($arr, $filterFn);
        return new self($this->type, ...array_values($filteredArr));
    }


}
