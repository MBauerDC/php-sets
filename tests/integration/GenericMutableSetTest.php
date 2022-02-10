<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\integration;

use MBauer\PhpSets\implementations\GenericElement;
use MBauer\PhpSets\implementations\GenericMutableSet;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use function spl_object_id;

class GenericMutableSetTest extends TestCase
{

    protected MockBuilder $genericElMockBuilder;

    public function setUp(): void
    {
        $this->genericElMockBuilder = $this->getMockBuilder(GenericElement::class);
    }

    protected function createElement(string $id, mixed $data): mixed
    {
        $mock = $this->genericElMockBuilder->setConstructorArgs([$data, $id])->getMock();
        $mock->expects($this->any())->method('getIdentifier')->willReturn($id);
        $mock->expects($this->any())->method('clone')->willReturn($mock);
        return $mock;
    }

    public function testFilterLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericMutableSet($el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $actualIds = $filteredSet->getElementIds();

        //var_dump($actualIds);
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->filter must leave all intended elements.');
    }


    public function testFilterLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericMutableSet($el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $expectedIds = ['a', 'b'];
        $actualIds = $filteredSet->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->filter must leave only intended elements.');
    }

    public function testWithoutLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->without must leave all intended elements.');
    }

    public function testWithoutLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $expectedIds = ['a', 'b'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->without must leave only intended elements.');
    }

    public function testIntersectWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el1, $el2);
        $set1WithoutSet2 = $set1->intersectWith($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->intersectWith must leave all intended elements.');
    }

    public function testIntersectWithLeavesOnlyIntended(): void
    {
        // Does not work with mocks - unexpected results - works with actual elements.
        /*$el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');*/
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');
        $el4 = new GenericElement('d','d');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el1, $el2);

        $intersection = $set1->intersectWith($set2);

        $expectedIds = ['a', 'b'];

        $actualIds = $intersection->getElementIds();

        $diff = \array_diff($actualIds, $expectedIds);

        $this->assertEmpty($diff, 'GenericMutableSet->intersectWith must leave only intended elements. Actual for object [' . spl_object_id($intersection) . ']: ' . json_encode($actualIds));

    }

    public function testIntersectWithMultipleSucceeds(): void
    {
        $elX = $this->createElement('X', 'X');
        $elY = $this->createElement('Y', 'Y');
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericMutableSet($el1, $el2, $elX, $elY);
        $set2 = new GenericMutableSet($el3, $el4, $elX, $elY);
        $set3 = new GenericMutableSet($el5, $el6, $elX, $elY);
        $set1WithoutSet2 = $set1->intersectWith($set2, $set3);
        $expectedIds = ['X', 'Y'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff1 = \array_diff($actualIds, $expectedIds);
        $diff2 = \array_diff($expectedIds, $actualIds);
        $bothEmpty = empty($diff1) && empty($diff2);
        $this->assertTrue($bothEmpty, 'GenericMutableSet->intersectWith multiple must succeed.');
    }

    public function testUnionWithHasAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2);
        $set2 = new GenericMutableSet($el3, $el4);
        $union = $set1->unionWith($set2);
        $actualIds = $union->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;

        $this->assertTrue($containsAllIntended, 'GenericMutableSet->unionWith must result in a set with all intended elements.');
    }

    public function testUnionWithHasOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2);
        $set2 = new GenericMutableSet($el3, $el4);
        $set1WithoutSet2 = $set1->unionWith($set2);
        $expectedIds = ['a', 'b', 'c', 'd'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->unionWith must result in a set with only intended elements.');
    }

    public function testUnionWithMultipleSucceeds(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericMutableSet($el1, $el2);
        $set2 = new GenericMutableSet($el3, $el4);
        $set3 = new GenericMutableSet($el5, $el6);
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
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->filter must leave all intended elements.');
    }

    public function testCloneHasOnlyOriginalElements(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $expectedIds = ['a', 'b', 'c'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->filter must leave only intended elements.');
    }

    public function testIsSubsetOfReturnsTrueForSuperset(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2);
        $set2 = new GenericMutableSet($el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for Superset.');
    }

    public function testIsSubsetOfReturnsTrueForSameSet(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for same set.');
    }

    public function testIsSubsetOfReturnsFalseForSubset(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set2 = new GenericMutableSet($el1, $el2);

        $this->assertFalse($set1->isSubsetOf($set2), 'Subset must return false when queried by isSubsetOf for own subset.');
    }

    public function testSymmetricDifferenceWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericMutableSet($el1, $el2, $el5, $el6);
        $set2 = new GenericMutableSet($el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->symmetricDifferenceWith must result in a set with all intended elements.');
    }

    public function testSymmetricDifferenceWithLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericMutableSet($el1, $el2, $el5, $el6);
        $set2 = new GenericMutableSet($el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->symmetricDifferenceWith must result in a set with only intended elements.');
    }

    public function testSymmetricDifferenceWithMultipleSucceeds(): void
    {
        $elX = $this->createElement('X', 'X');
        $elY = $this->createElement('Y', 'Y');
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericMutableSet($el1, $el2, $elX, $elY);
        $set2 = new GenericMutableSet($el3, $el4, $elX);
        $set3 = new GenericMutableSet($el5, $el6, $elY);
        $set1WithoutSet2 = $set1->symmetricDifferenceWith($set2, $set3);
        $expectedIds = ['a', 'b', 'c', 'd', 'e', 'f'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff1 = \array_diff($actualIds, $expectedIds);
        $diff2 = \array_diff($expectedIds, $actualIds);
        $bothEmpty = empty($diff1) && empty($diff2);
        $this->assertTrue($bothEmpty, 'GenericMutableSet->symmetricDifferenceWith multiple must succeed.');
    }

    public function testAddElementsHasAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2);
        $set->addElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->addElements must result in a Set with all intended elements.');
    }

    public function testAddElementHasOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2);
        $set->addElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->addElements must result in a set with only intended elements.');
    }

    public function removeElementsLeavesAllIntended(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set->removeElements($el3, $el4);
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->removeElements must result in a Set with all intended elements.');
    }

    public function removeElementsLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set->removeElementsById($el3, $el4);
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->removeElements must result in a set with only intended elements.');

    }

    public function removeElementsByIdLeavesAllIntended(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set->removeElementsById($el3->getIdentifier(), $el4->getIdentifier());
        $actualIds = $set->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericMutableSet->removeElementsById must result in a Set with all intended elements.');
    }

    public function removeElementsByIdLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set = new GenericMutableSet($el1, $el2, $el3, $el4);
        $set->removeElementsById($el3->getIdentifier(), $el4->getIdentifier());
        $actualIds = $set->getElementIds();
        $expectedIds = ['a', 'b'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericMutableSet->removeElementsById must result in a set with only intended elements.');
    }

    public function testHasElementByIdSucceedsForPresentElement(): void
    {
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $found = $set->hasElementById('b');
        $this->assertSame(true, $found, 'GenericSet->hasElementById must return true for present element.');
    }

    public function testHasElementByIdFailsForMissingElement(): void
    {
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $found = $set->hasElementById('d');
        $this->assertSame(false, $found, 'GenericSet->hasElementById must return false for missing element.');
    }

    public function testGetElementByIdSucceedsForPresentElement(): void
    {
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $found = $set->getElementById('b');
        $this->assertSame($el2, $found, 'GenericSet->getElemenetById must return the given element.');
    }

    public function testGetElementByIdReturnsNullForMissingElement(): void
    {
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');

        $set = new GenericMutableSet($el1, $el2, $el3);
        $found = $set->getElementById('d');
        $this->assertNull($found, 'GenericSet->getElemenetById must return null for missing element.');
    }
}
