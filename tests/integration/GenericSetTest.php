<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\integration;

use MBauer\PhpSets\implementations\GenericElement;
use MBauer\PhpSets\implementations\GenericSet;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use function spl_object_id;

class   GenericSetTest extends TestCase
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
        $set = new GenericSet($el1, $el2, $el3);
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
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $filterFn = fn($el) => $el->getIdentifier() !== 'c';
        $set = new GenericSet($el1, $el2, $el3);
        $filteredSet = $set->filter($filterFn);
        $expectedIds = ['a', 'b'];
        $actualIds = $filteredSet->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->filter must leave only intended elements.');
    }

    public function testWithoutLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->without must leave all intended elements.');
    }

    public function testWithoutLeavesOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el3, $el4);
        $set1WithoutSet2 = $set1->without($set2);
        $expectedIds = ['a', 'b'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->without must leave only intended elements.');
    }

    public function testIntersectWithLeavesAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el1, $el2);
        $set1WithoutSet2 = $set1->intersectWith($set2);
        $actualIds = $set1WithoutSet2->getElementIds();
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
        $el1 = new GenericElement('a','a');
        $el2 = new GenericElement('b','b');
        $el3 = new GenericElement('c','c');
        $el4 = new GenericElement('d','d');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el1, $el2);

        $intersection = $set1->intersectWith($set2);

        $expectedIds = ['a', 'b'];

        $actualIds = $intersection->getElementIds();

        $diff = \array_diff($actualIds, $expectedIds);

        $this->assertEmpty($diff, 'GenericSet->intersectWith must leave only intended elements. Actual for object [' . spl_object_id($intersection) . ']: ' . json_encode($actualIds));

    }

    public function testUnionWithHasAllIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2);
        $set2 = new GenericSet($el3, $el4);
        $union = $set1->unionWith($set2);
        $actualIds = $union->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsC = \in_array('c', $actualIds);
        $containsD = \in_array('d', $actualIds);
        $containsAllIntended = $containsA && $containsB && $containsC && $containsD;

        $this->assertTrue($containsAllIntended, 'GenericSet->unionWith must result in a set with all intended elements.');
    }

    public function testUnionWithHasOnlyIntended(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2);
        $set2 = new GenericSet($el3, $el4);
        $set1WithoutSet2 = $set1->unionWith($set2);
        $expectedIds = ['a', 'b', 'c', 'd'];
        $actualIds = $set1WithoutSet2->getElementIds();
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->unionWith must result in a set with only intended elements.');
    }

    public function testCloneHasAllOriginalElements(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $set = new GenericSet($el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $containsA = \in_array('a', $actualIds);
        $containsB = \in_array('b', $actualIds);
        $containsAllIntended = $containsA && $containsB;
        $this->assertTrue($containsAllIntended, 'GenericSet->filter must leave all intended elements.');
    }

    public function testCloneHasOnlyOriginalElements(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');

        $set = new GenericSet($el1, $el2, $el3);
        $clonedSet = $set->clone();
        $actualIds = $clonedSet->getElementIds();
        $expectedIds = ['a', 'b', 'c'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->filter must leave only intended elements.');
    }

    public function testIsSubsetOfReturnsTrueForSuperset(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2);
        $set2 = new GenericSet($el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for Superset.');
    }

    public function testIsSubsetOfReturnsTrueForSameSet(): void
    {

        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el1, $el2, $el3, $el4);

        $this->assertTrue($set1->isSubsetOf($set2), 'Subset must return true when queried by isSubsetOf for same set.');
    }

    public function testIsSubsetOfReturnsFalseForSubset(): void
    {
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');

        $set1 = new GenericSet($el1, $el2, $el3, $el4);
        $set2 = new GenericSet($el1, $el2);

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

        $set1 = new GenericSet($el1, $el2, $el5, $el6);
        $set2 = new GenericSet($el3, $el4, $el5, $el6);
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
        $el1 = $this->createElement('a', 'a');
        $el2 = $this->createElement('b', 'b');
        $el3 = $this->createElement('c', 'c');
        $el4 = $this->createElement('d', 'd');
        $el5 = $this->createElement('e', 'e');
        $el6 = $this->createElement('f', 'f');

        $set1 = new GenericSet($el1, $el2, $el5, $el6);
        $set2 = new GenericSet($el3, $el4, $el5, $el6);
        $symmetricDifferenceWith = $set1->symmetricDifferenceWith($set2);
        $actualIds = $symmetricDifferenceWith->getElementIds();
        $expectedIds = ['a', 'b', 'c', 'd'];
        $diff = \array_diff($actualIds, $expectedIds);
        $this->assertEmpty($diff, 'GenericSet->symmetricDifferenceWith must result in a set with only intended elements.');
    }
}
