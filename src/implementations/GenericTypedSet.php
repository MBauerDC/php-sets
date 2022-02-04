<?php
declare(strict_types=1);

use MBauer\PhpSets\contracts\Element;
use MBauer\PhpSets\contracts\Set;
use MBauer\PhpSets\contracts\TypedElement;
use MBauer\PhpSets\contracts\TypedSet;


namespace MBauer\PhpSets\implementations;

/**
 * @template T
 * @implements TypedSet<T>
 */
class GenericTypedSet implements TypedSet
{
    public readonly string $type;
    protected $elements = [];

    /**
     * @param class-string<T> $type
     * @param TypedElement<T>[] $els
     */
    public function __construct(string $type, TypedElement ...$els)
    {
        $this->type = $type;
        $this->addElements(...$els);
    }

    /**
     * @inheritdoc
     */
    public function getElementIds(): array 
    {
        return \array_keys($this->elements);
    }

    /**
     * @param TypedElement<T>[] $els
     */
    protected function addElements(TypedElement ...$els): void
    {
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
     * @inheritdoc
     */
    public function toArray(): array
    {
        $clones = [];
        foreach ($this->elements as $id => $el) {
            $clones[$id] = $el->clone();
        }
        return $clones;
    }

    /**
     * @inheritdoc
     */
    public function isSubsetOf(TypedSet $set): bool
    {
        if ($set->getType() !== $this->type) {
            return false;
        }
        $theseIds = $this->getElementIds();
        $theseIds = array_combine($theseIds, $theseIds);
        $thoseIds = $set->getElementIds();
        $thoseIds = array_combine($thoseIds, $thoseIds);
        $thisCount = \count($theseIds);
        return \count(\array_intersect_key($thoseIds, $theseIds)) === $thisCount;
    }


    /**
     * @param TypedSet[] $sets
     * @return TypedSet<T>[]
     */
    protected function filterSetsForSameType(array $sets): array
    {
        $thisType = $this->type;
        $allTypes = \array_map(static fn(TypedSet $s) => $s->getType(), $sets);
        $setsCount = \count($sets);
        $setsOfSameType = [];
        foreach ($i = 0; $i < $setsCount; $i++) {
            $currType = $allTypes[$i];
            if ($currType === $thisType) {
                $setsOfSameType[] = $sets[$i];
            }
        };
        return $setsOfSameType;
    }

    /**
     * @inheritdoc
     */
    public function without(TypedSet ...$sets): Set
    {
        if (\count($sets) === 0) {
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
        return new static($this->type, ...$new);
    }

    /**
     * @inheritdoc
     */
    public function intersectWith(Set ...$sets): Set
    {
        $origCount = \count($sets);
        if ($origCount === 0) {
            return $this->clone();
        }
        
        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = \count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            return new static($this->type);
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
        return new static($this->type, ...$new);
    }

    /**
     * @inheritdoc
     */
    public function unionWith(Set ...$sets): Set
    {
        $origCount = \count($sets);
        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = \count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            throw new InvalidArgumentException('Can only union with other TypedSets of type [' . $this->type . '].');
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = $otherArr + $currArr;
        }
        return new static($this->type, ...\array_values($currArr));
    }

    /**
     * @inheritdoc
     */
    public function symmetricDifferenceWith(Set ...$sets): Set
    {
        $origCount = \count($sets);
        if ($origCount === 0) {
            return $this->clone();
        }
        $setsOfSameType = $this->filterSetsForSameType($sets);
        $sameTypeCount = \count($setsOfSameType);
        if ($origCount !== $sameTypeCount) {
            throw new InvalidArgumentException('Can only create symmetric difference with other TypedSets of type [' . $this->type . '].');
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = \array_diff_key($currAr, $otherArr) + \array_diff_key($otherArr, $currArr);
        }
        return new static($this->type, ...\array_values($currArr));
    }

}