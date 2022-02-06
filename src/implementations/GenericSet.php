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
        return new self(...$new);
    }

    #[Pure]
    public function unionWith(Set ...$sets): GenericSet
    {
        $currArr = $this->toArray();
        foreach ($sets as $set) {
            $otherArr = $set->toArray();
            $currArr = $otherArr + $currArr;
        }
        return new self(...array_values($currArr));
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
        return new self(...array_values($currArr));
    }

    /**
     * @param pure-callable(Element):bool $filterFn
     * @return GenericBaseSet
     */
    #[Pure]
    public function filter(callable $filterFn): GenericSet
    {
        $newArr = array_map(static fn(Element $el) => $el->clone(),array_filter($this->elements, $filterFn));
        return new self(...$newArr);
    }

    #[Pure]
    public function clone(): GenericSet
    {
        $newArr = array_map(static fn(Element $el) => $el->clone(), $this->elements);
        return new self(...$newArr);
    }

}
