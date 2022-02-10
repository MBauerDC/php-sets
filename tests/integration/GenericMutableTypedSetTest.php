<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\integration;

use MBauer\PhpSets\implementations\ElementBehavior;
use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\GenericMutableTypedSet;
use PHPUnit\Framework\TestCase;
use function spl_object_id;

class GenericMutableTypedSetTest extends TestCase
{

    protected function createElement(string $type, string $id, mixed $data): mixed
    {
        return new GenericTypedElement($type, $data, $id, ElementBehavior::DATA_COPY_ASSIGNMENT);
    }

    public function testFilterLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $actualIds = $filteredSet->getElementIds();

        //var_dump($actualIds);
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->filter must leave all intended elements.');
    }

    public function testCreateWithTypeMatchedElementSucceeds(): void
    {
        $el = $this->createElement('string', 'a', 'b');
        try {
            $set = new GenericMutableTypedSet('string', $el);
            $varClass = get_class($set);
            $this->assertInstanceOf(GenericMutableTypedSet::class,$set,'Creating TypedSet with type-matched Element must succeed. Actual type [' . $varClass . '].');
        } catch (\Throwable $t) {
            $this->fail('Creating TypedSet with type-matched Element must succeed. Error: ' . $t->getMessage());
        }
    }

    public function testCreateWithTypeMismatchedElementFails(): void
    {
        $el = $this->createElement('array', 'a', ['a']);
        $set = null;
        try {
            $set = new GenericMutableTypedSet('string', $el);
            $this->fail('Creating TypedSet with type-matched Element must succeed.');
        } catch (\Throwable $t) {
            $this->assertNull($set, 'Creating TypedSet with type-matched Element must succeed.');
        }
    }

    public function testFilterLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $expectedIds = ['a', 'b'];
        $actualIds = $filteredSet->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->filter must leave only intended elements.');
    }

    public function testWithoutLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->without must leave all intended elements.');
    }

    public function testWithoutLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $expectedIds = ['a', 'b'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->without must leave only intended elements.');
    }

    public function testIntersectWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el1, $el2);
        $set1WithoutSet2 = $set1->intersectWith($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->intersectWith must leave all intended elements.');
    }

    public function testIntersectWithLeavesOnlyIntended(): void
    {
        // Does not work with mocks - unexpected results - works with actual elements.
        /*$el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');*/
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('string', 'c','c');
        $el4 = new GenericTypedElement('string', 'd','d');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el1, $el2);

        $intersection = $set1->intersectWith($set2);

        $expectedIds = ['a', 'b'];

        $actualIds = $intersection->getElementIds();

        $diff = \array_diff($actualIds, $expectedIds);

        $this->assertEmpty($diff, 'GenericMutableTypedSet->intersectWith must leave only intended elements. Actual for object [' . spl_object_id($intersection) . ']: ' . json_encode($actualIds));

    }

    public function testIntersectWithMultipleSucceeds(): void
    {
        $elX = $this->createElement('string','X', 'X');
        $elY = $this->createElement('string','Y', 'Y');
        $el1 = $this->createElement('string','a', 'a');
        $el2 = $this->createElement('string','b', 'b');
        $el3 = $this->createElement('string','c', 'c');
        $el4 = $this->createElement('string','d', 'd');
        $el5 = $this->createElement('string','e', 'e');
        $el6 = $this->createElement('string','f', 'f');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $elX, $elY);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4, $elX, $elY);
        $set3 = new GenericMutableTypedSet('string', $el5, $el6, $elX, $elY);
        $set1WithoutSet2 = $set1->intersectWith($set2, $set3);
        $expectedIds = ['X', 'Y'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff1 = \array_diff($actualIds, $expectedIds);
        $diff2 = \array_diff($expectedIds, $actualIds);
        $bothEmpty = empty($diff1) && empty($diff2);
        $this->assertTrue($bothEmpty, 'GenericMutableTypedSet->intersectWith multiple must succeed.');
    }

    public function testUnionWithHasAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4);
        $union = $set1->unionWith($set2);
        $actualIds = $union->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;

        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->unionWith must result in a set with all intended elements.');
    }

    public function testUnionWithHasOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4);
        $set1WithoutSet2 = $set1->unionWith($set2);
        $expectedIds = ['a', 'b', 'c', 'd'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->unionWith must result in a set with only intended elements.');
    }

    public function testUnionWithMultipleSucceeds(): void
    {
        $el1 = $this->createElement('string','a', 'a');
        $el2 = $this->createElement('string','b', 'b');
        $el3 = $this->createElement('string','c', 'c');
        $el4 = $this->createElement('string','d', 'd');
        $el5 = $this->createElement('string','e', 'e');
        $el6 = $this->createElement('string','f', 'f');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4);
        $set3 = new GenericMutableTypedSet('string', $el5, $el6);
        $set1WithoutSet2 = $set1->unionWith($set2, $set3);
        $expectedIds = ['a', 'b', 'c', 'd', 'e', 'f'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff1 = \array_diff($actualIds, $expectedIds);
        $diff2 = \array_diff($expectedIds, $actualIds);
        $bothEmpty = empty($diff1) && empty($diff2);
        $this->assertTrue($bothEmpty, 'GenericMutableSet->unionWith multiple must succeed.');
    }

    public function testCloneHasAllOriginalElements(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->filter must leave all intended elements.');
    }

    public function testCloneHasOnlyOriginalElements(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $expectedIds = ['a', 'b', 'c'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->filter must leave only intended elements.');
    }

    public function testIsSubsetOfReturnsTrueForSuperset(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2);
        $set2 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for Superset.');
    }

    public function testIsSubsetOfReturnsTrueForSameSet(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for same set.');
    }

    public function testIsSubsetOfReturnsFalseForSubset(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set2 = new GenericMutableTypedSet('string', $el1, $el2);

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

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el5, $el6);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->symmetricDifferenceWith must result in a set with all intended elements.');
    }

    public function testSymmetricDifferenceWithLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');
        $el5 = $this->createElement('string', 'e', 'e');
        $el6 = $this->createElement('string', 'f', 'f');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $el5, $el6);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->symmetricDifferenceWith must result in a set with only intended elements.');
    }
    public function testSymmetricDifferenceWithMultipleSucceeds(): void
    {
        $elX = $this->createElement('string','X', 'X');
        $elY = $this->createElement('string','Y', 'Y');
        $el1 = $this->createElement('string','a', 'a');
        $el2 = $this->createElement('string','b', 'b');
        $el3 = $this->createElement('string','c', 'c');
        $el4 = $this->createElement('string','d', 'd');
        $el5 = $this->createElement('string','e', 'e');
        $el6 = $this->createElement('string','f', 'f');

        $set1 = new GenericMutableTypedSet('string', $el1, $el2, $elX, $elY);
        $set2 = new GenericMutableTypedSet('string', $el3, $el4, $elX);
        $set3 = new GenericMutableTypedSet('string', $el5, $el6, $elY);
        $set1WithoutSet2 = $set1->symmetricDifferenceWith($set2, $set3);
        $expectedIds = ['a', 'b', 'c', 'd', 'e', 'f'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff1 = \array_diff($actualIds, $expectedIds);
        $diff2 = \array_diff($expectedIds, $actualIds);
        $bothEmpty = empty($diff1) && empty($diff2);
        $this->assertTrue($bothEmpty, 'GenericMutableTypedSet->symmetricDifferenceWith multiple must succeed.');
    }


    public function testAddElementOfMismatchedTypeFails(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('array', 'c', ['c']);
        $set = new GenericMutableTypedSet('string', $el1, $el2);
        $this->expectException(\InvalidArgumentException::class);
        $set->addElements($el3);
        $succeeded = true;
        $this->assertFalse($succeeded, 'Adding Element of mismatched type must result in InvalidArgumentException');
    }

    public function testAddElementsHasAllIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2);
        $set->addElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->addElements must result in a Set with all intended elements.');
    }

    public function testAddElementHasOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2);
        $set->addElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->addElements must result in a set with only intended elements.');
    }

    public function removeElementsLeavesAllIntended(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set->removeElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->removeElements must result in a Set with all intended elements.');
    }

    public function removeElementsLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set->removeElementsById($el3, $el4);
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->removeElements must result in a set with only intended elements.');

    }

    public function removeElementsByIdLeavesAllIntended(): void
    {

        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set->removeElementsById($el3->getIdentifier(), $el4->getIdentifier());
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableTypedSet->removeElementsById must result in a Set with all intended elements.');
    }

    public function removeElementsByIdLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('string', 'a', 'a');
        $el2 = $this->createElement('string', 'b', 'b');
        $el3 = $this->createElement('string', 'c', 'c');
        $el4 = $this->createElement('string', 'd', 'd');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3, $el4);
        $set->removeElementsById($el3->getIdentifier(), $el4->getIdentifier());
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableTypedSet->removeElementsById must result in a set with only intended elements.');
    }

    public function testHasElementByIdSucceedsForPresentElement(): void
    {
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('string', 'c','c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $found = $set->hasElementById('b');
        $this->assertSame(true, $found, 'GenericMutableTypedSet->hasElementById must return true for present element.');
    }

    public function testHasElementByIdFailsForMissingElement(): void
    {
        $el1 = new GenericTypedElement('string','a','a');
        $el2 = new GenericTypedElement('string','b','b');
        $el3 = new GenericTypedElement('string','c','c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $found = $set->hasElementById('d');
        $this->assertSame(false, $found, 'GenericMutableTypedSet->hasElementById must return false for missing element.');
    }

    public function testGetElementByIdSucceedsForPresentElement(): void
    {
        $el1 = new GenericTypedElement('string', 'a','a');
        $el2 = new GenericTypedElement('string', 'b','b');
        $el3 = new GenericTypedElement('string', 'c','c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $found = $set->getElementById('b');
        $this->assertSame($el2, $found, 'GenericMutableTypedSet->getElemenetById must return the given element.');
    }

    public function testGetElementByIdReturnsNullForMissingElement(): void
    {
        $el1 = new GenericTypedElement('string','a','a');
        $el2 = new GenericTypedElement('string','b','b');
        $el3 = new GenericTypedElement('string','c','c');

        $set = new GenericMutableTypedSet('string', $el1, $el2, $el3);
        $found = $set->getElementById('d');
        $this->assertNull($found, 'GenericSet->getElemenetById must return null for missing element.');
    }
}
