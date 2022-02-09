<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\integration;

use MBauer\PhpSets\implementations\ElementBehavior;
use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\GenericTypedSet;
use PHPUnit\Framework\TestCase;
use function \spl_object_id;

class GenericTypedSetTest extends TestCase
{

    protected function createElement(
        string $type,
        string $id,
        mixed $data,
        ElementBehavior $behavior = ElementBehavior::DATA_COPY_ASSIGNMENT
    ): GenericTypedElement {
        return new GenericTypedElement($type, $data, $id, $behavior);
    }

    public function testCreateWithTypeMatchedElementSucceeds(): void
    {
        $el = $this->createElement('string', 'a', 'b');
        try {
            $set = new GenericTypedSet('string', $el);
            $varClass = get_class($set);
            $this->assertInstanceOf(GenericTypedSet::class,$set,'Creating TypedSet with type-matched Element must succeed. Actual type [' . $varClass . '].');
        } catch (\Throwable $t) {
            $this->fail('Creating TypedSet with type-matched Element must succeed. Error: ' . $t->getMessage());
        }
    }

    public function testCreateWithTypeMismatchedElementFails(): void
    {
        $el = $this->createElement('array', 'a', ['a']);
        $set = null;
        try {
            $set = new GenericTypedSet('string', $el);
            $this->fail('Creating TypedSet with type-matched Element must succeed.');
        } catch (\Throwable $t) {
            $this->assertNull($set, 'Creating TypedSet with type-matched Element must succeed.');
        }
    }

    public function testFilterLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericTypedSet('string', $el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $actualIds = $filteredSet->getElementIds();

        //var_dump($actualIds);
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->filter must leave all intended elements.');
    }


    public function testFilterLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericTypedSet('string', $el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $expectedIds = ['a', 'b'];
        $actualIds = $filteredSet->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->filter must leave only intended elements.');
    }

    public function testWithoutLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->without must leave all intended elements.');
    }

    public function testWithoutLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $expectedIds = ['a', 'b'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->without must leave only intended elements.');
    }

    public function testIntersectionWithSetOfMismatchedTypeIsEmpty(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('array', 'c', ['c']);
        $el4 = $this->createElement('array', 'd', ['d']);
        $set1 = new GenericTypedSet('string', $el1, $el2);
        $set2 = new GenericTypedSet('array', $el3, $el4);
        $intersection = $set1->intersectWith($set2);
        $elementIds = $intersection->getElementIds();
        $this->assertCount(0, $elementIds, 'Intersection with set of mismatched type must be empty.');
    }

    public function testIntersectWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el1, $el2);
        $intersection = $set1->intersectWith($set2);
        $actualIds = $intersection->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->intersectWith must leave all intended elements.');
    }

    public function testIntersectWithLeavesOnlyIntended(): void
    {
        // Does not work with mocks - unexpected results - works with actual elements.
        /*$el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');*/
        $el1 = new GenericTypedElement('string','a','a');
        $el2 = new GenericTypedElement('string','b','b');
        $el3 = new GenericTypedElement('string','c','c');
        $el4 = new GenericTypedElement('string','d','d');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el1, $el2);

        $intersection = $set1->intersectWith($set2);

        $expectedIds = ['a', 'b'];

        $actualIds = $intersection->getElementIds();

        $diff = \array_diff($actualIds, $expectedIds);

        $this->assertEmpty($diff, 'GenericSet->intersectWith must leave only intended elements. Actual for object [' . spl_object_id($intersection) . ']: ' . json_encode($actualIds));

    }

    public function testUnionWithMismatchedTypeIsOriginal(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('array', 'c', ['c']);
        $el4 = $this->createElement('array', 'd', ['d']);

        $set1 = new GenericTypedSet('string', $el1, $el2);
        $set2 = new GenericTypedSet('array', $el3, $el4);
        $union = $set1->unionWith($set2);
        $actualIds = $union->getElementIds();
        $expectedIds = ['a', 'b'];
        $diff1 = array_diff($actualIds, $expectedIds);
        $diff2 = array_diff($expectedIds, $actualIds);
        $unionIsOriginal = empty($diff1) && empty($diff2);
        $this->assertTrue($unionIsOriginal, 'Union with set of mismatched type must result in set with original entries.');
    }

    public function testUnionWithHasAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2);
        $set2 = new GenericTypedSet('string', $el3, $el4);
        $union = $set1->unionWith($set2);
        $actualIds = $union->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;

        $this->assertTrue($containsAllIntended, 'GenericSet->unionWith must result in a set with all intended elements. Contained elements are: [' . implode(', ', $actualIds) . '].');
    }

    public function testUnionWithHasOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2);
        $set2 = new GenericTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->unionWith($set2);
        $expectedIds = ['a', 'b', 'c', 'd'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->unionWith must result in a set with only intended elements.');
    }

    public function testCloneHasAllOriginalElements(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $set = new GenericTypedSet('string', $el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->filter must leave all intended elements.');
    }

    public function testCloneHasOnlyOriginalElements(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $set = new GenericTypedSet('string', $el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $expectedIds = ['a', 'b', 'c'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->filter must leave only intended elements.');
    }

    public function testIsSubsetOfReturnsTrueForSuperset(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2);
        $set2 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for Superset.');
    }

    public function testIsSubsetOfReturnsTrueForSameSet(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for same set.');
    }

    public function testIsSubsetOfReturnsFalseForSubset(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericTypedSet('string', $el1, $el2);

        $this->assertFalse($set1->isSubsetOf($set2), 'Subset must return false when queried by isSubsetOf for own subset.');
    }

    public function testSymmetricDifferenceWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');
        $el5 = $this->createElement('string', 'e', 'e');
        $el6 = $this->createElement('string', 'f', 'f');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el5, $el6);
        $set2 = new GenericTypedSet('string', $el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;
        $this->assertTrue($containsAllIntended, 'GenericSet->symmetricDifferenceWith must result in a set with all intended elements.');
    }

    public function testSymmetricDifferenceWithLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');
        $el5 = $this->createElement('string', 'e', 'e');
        $el6 = $this->createElement('string', 'f', 'f');

        $set1 = new GenericTypedSet('string', $el1, $el2, $el5, $el6);
        $set2 = new GenericTypedSet('string', $el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->symmetricDifferenceWith must result in a set with only intended elements.');
    }

    public function testCanUseSetAsElement(): void
    {
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('string', 'c','c');
        $el4 = new GenericTypedElement('string', 'd','d');

        try {
            $set1 = new GenericTypedSet('string', $el1, $el2);
            $set2 = new GenericTypedSet('string', $el3, $el4, $set1);
            $this->assertInstanceOf(GenericTypedSet::class, $set2);
        } catch (\Throwable $t) {
            $this->fail('Must be able to add set as element to set. Error: [' . $t->getMessage() . '].');
        }
    }

    public function testCannotUseSetOfDifferentTypeAsElement(): void
    {
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('array', ['c'],'c');
        $el4 = new GenericTypedElement('array', ['d'],'d');

        try {
            $set1 = new GenericTypedSet('string', $el1, $el2);
            $set2 = new GenericTypedSet('array', $el3, $el4, $set1);
            $this->fail('Must not be able to add set of different type as element to set.');
        } catch (\Throwable $t) {
            $this->assertTrue(true);
        }
    }

    public function testGetSetUsedAsElement(): void
    {
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('string', 'c','c');
        $el4 = new GenericTypedElement('string', 'd','d');

        try {
            $set1 = new GenericTypedSet('string', $el1, $el2);
            $set2 = new GenericTypedSet('string', $el3, $el4, $set1);
            $elements = $set2->toArray();
            $this->assertInstanceOf(GenericTypedSet::class, $set2);
        } catch (\Throwable $t) {
            $this->fail('Must be able to add set as element to set. Error: [' . $t->getMessage() . '].');
        }
    }
}
