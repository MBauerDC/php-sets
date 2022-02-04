<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Mbauer\PhpSets\contracts\Set;
use Mbauer\PhpSets\contracts\Element;
use function array_diff_key;
use function array_intersect_key;
use function array_keys;
use function array_values;
use function count;

class GenericSet implements Set
{
    protected $elements = [];

    public function __construct(Element ...$els)
    {
        $this->addElements(...$els);
    }

    /**
     * @inheritdoc
     */
    public function getElementIds(): array 
    {
        return array_keys($this->elements);
    }

    protected function addElements(Element ...$els): void
    {
        foreach ($els as $el) {
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
    public function isSubsetOf(Set $set): bool
    {
        $theseIds = $this->getElementIds();
        $theseIds = array_combine($theseIds, $theseIds);
        $thoseIds = $set->getElementIds();
        $thoseIds = array_combine($thoseIds, $thoseIds);
        $thisCount = count($theseIds);
        return count(array_intersect_key($thoseIds, $theseIds)) === $thisCount;
    }

    /**
     * @inheritdoc
     */
    public function without(Set ...$sets): Set
    {
        if (count($sets) === 0) {
            return $this->clone();
        }
        $new = [];
        $theseElements = $this->toArray();
        foreach ($theseElements as $key => $el) {
            $inAnyRHS = false;
            foreach ($sets as $set) {
                if ($set->hasElementById($key)) {
                    $inAnyRHS = true;
                    break;
                }
            }
            if (!$inAnyRHS) {
                $new[] = $el;
            }
        }
        return new static(...$new);
    }

    /**
     * @inheritdoc
     */
    public function intersectWith(Set ...$sets): Set
    {
        if (count($sets) === 0) {
            return $this->clone();
        }
        $new = [];
        $theseIds = $this->getElementIds();
        
        foreach ($theseIds as $idFromThis) {
            $inAll = true;
            foreach ($sets as $set) {
                if (!$set->hasElementById($idFromThis)) {
                    $inAll = false;
                    break;
                }
            }
            if ($inAll) {
                $new[] = $this->elements[$idFromThis]->clone();
            }
        }
        return new static(...$new);
    }

    /**
     * @inheritdoc
     */
    public function unionWith(Set ...$sets): Set
    {
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = $otherArr + $currArr;
        }
        return new static(...array_values($currArr));
    }

    /**
     * @inheritdoc
     */
    public function symmetricDifferenceWith(Set ...$sets): Set
    {
        if (count($sets) === 0) {
            return $this->clone();
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = array_diff_key($currAr, $otherArr) + array_diff_key($otherArr, $currArr);
        }
        return new static(...array_values($currArr));
    }

}