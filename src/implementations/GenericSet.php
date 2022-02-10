<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Set;
use MBauer\PhpSets\contracts\Element;
use Psalm\Immutable;
use Psalm\Pure;
use function array_key_exists;

#[Immutable]
class GenericSet extends GenericBaseSet implements Set
{
    use ProvidesElementCheck, ProvidesElements;


    #[Pure]
    public function isSubsetOf(Set $set): bool
    {
        $theseIds = $this->getElementIds();
        $theseIds = array_combine($theseIds, $theseIds);
        $thoseIds = $set->getElementIds();
        $thoseIds = array_combine($thoseIds, $thoseIds);
        $thisCount = count($theseIds);
        return count(array_intersect_key($thoseIds, $theseIds)) === $thisCount;
    }

    #[Pure]
    public function without(Set ...$sets): GenericSet
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
        return new self(...$new);
    }

    #[Pure]
    public function intersectWith(Set ...$sets): GenericSet
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
        $new = new self(...$new);
        return $new;
    }

    #[Pure]
    public function unionWith(Set ...$sets): GenericSet
    {
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = $otherArr + $currArr;
        }
        $new = new self(...($currArr));
        return $new;

    }

    #[Pure]
    public function symmetricDifferenceWith(Set ...$sets): GenericSet
    {
        if (count($sets) === 0) {
            return $this->clone();
        }
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = array_diff_key($currArr, $otherArr) + array_diff_key($otherArr, $currArr);
        }
        return new self(...($currArr));
    }

    /**
     * @param pure-callable(Element):bool $filterFn
     * @return GenericBaseSet
     */
    #[Pure]
    public function filter(callable $filterFn): GenericSet
    {
        $filtered = array_filter($this->elements, $filterFn);
        $cloned = array_map(static fn(Element $el) => $el->clone(), $filtered);
        return new self(...$cloned);
    }

    #[Pure]
    public function clone(): GenericSet
    {
        $newArr = array_map(static fn(Element $el) => $el->clone(), $this->elements);
        return new self(...$newArr);
    }

    public function getPowerSet(): GenericSet
    {
        $count = $this->count();
        $ids = array_keys($this->elements);

        $subsets = [];
        $size = pow(2, $count);
        for ($i = 0; $i < $size; $i++) {
            $newElementSet = [];
            for ($j = 0; $j < $count; $j++) {
                if (($i>>$j) & 1) {
                    $newElementSet[] = $this->elements[$ids[$j]];
                }
            }
            $subsets[] = new GenericSet(...$newElementSet);
        }
        return new GenericSet(...$subsets);
    }

}
