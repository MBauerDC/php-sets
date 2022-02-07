<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\ElementBehavior;
use MBauer\PhpSets\implementations\GenericTypedElement;
use PHPUnit\Framework\TestCase;

class GenericTypedElementTest extends TestCase
{
    public function testGetIdentifier(): void
    {
        $expected = 'a';
        $el = new GenericTypedElement('string', 'a',$expected, ElementBehavior::DATA_COPY_ASSIGNMENT);
        $this->assertEquals($expected, $el->getIdentifier(), 'Element->getIdentifier must return given id.');
    }

    public function testCloneHasSameId(): void
    {
        $el = new GenericTypedElement('string', 'a','a', ElementBehavior::DATA_COPY_ASSIGNMENT);
        $clonedEl = $el->clone();
        $this->assertEquals($el->id, $clonedEl->id, 'Element->clone must return an element with the same id.');
    }

    public function testCloneWithCopyAssignment(): void
    {
        $a = new \stdClass();
        $a->prop1 = '1';
        $el = new GenericTypedElement(\stdClass::class,$a, 'a', ElementBehavior::DATA_COPY_ASSIGNMENT);
        $clonedEl = $el->clone();
        $a->prop1 = '2';
        $elA = $el->getData();
        $cloneA = $clonedEl->getData();
        $this->assertEquals($elA->prop1, $cloneA->prop1, 'Element->clone with copy-assignment behavior configuration must copy object-ref to data.');
    }

    public function testGetDataWithDataCloneWillCloneDataOnConstruct(): void
    {
        $a = new class() {
            public string $val = '1';
        };
        $el = new GenericTypedElement($a::class,$a, 'a', ElementBehavior::DATA_CLONE_WHERE_POSSIBLE);
        $a->val = '2';
        $elA = $el->getData();
        $this->assertNotEquals($a->val, $elA->val, 'Element::__construct with clone-assignment behavior configuration must not copy object-ref for cloneable data.');
    }

    public function testCloneWithDataCloneWillCloneDataAgain(): void
    {
        $a = new class() {
            public string $val = '1';
        };
        $el = new GenericTypedElement($a::class, $a,'a', ElementBehavior::DATA_CLONE_WHERE_POSSIBLE);
        $clonedEl = $el->clone();
        $elA = $el->getData();
        $elA->val = '2';
        $cloneA = $clonedEl->getData();
        $this->assertNotEquals($elA->val, $cloneA->val, 'Element->clone with clone-assignment behavior configuration must not copy object-ref for cloneable data.');
    }

    public function testGetDataWithWeakRef(): void
    {
        $a = new \stdClass();
        $el = new GenericTypedElement($a::class, $a,'a', ElementBehavior::DATA_COPY_WITH_WEAKREF);
        unset($a);
        $elData = $el->getData();
        $this->assertNull($elData, 'Element->getData with weak-ref behavior configuration must have null data after unsetting referenced value.');
    }

    public function testCloneWithWeakRef(): void
    {
        $a = new \stdClass();
        $el = new GenericTypedElement($a::class, $a,'a', ElementBehavior::DATA_COPY_WITH_WEAKREF);
        $clonedEl = $el->clone();
        unset($a);
        $cloneA = $clonedEl->getData();
        $this->assertNull($cloneA, 'Element->clone with weak-ref behavior configuration must have null data after unsetting referenced value.');
    }

    public function testCloneAsElementHasSameId(): void
    {
        $el = new GenericTypedElement('string', 'a','a', ElementBehavior::DATA_COPY_ASSIGNMENT);
        $clonedEl = $el->cloneAsElement();
        $this->assertEquals($el->id, $clonedEl->id, 'Element->clone must return an element with the same id.');

    }

    public function testCloneAsElementWithCopyAssignment(): void
    {
        $a = new \stdClass();
        $a->prop1 = '1';
        $el = new GenericTypedElement(\stdClass::class,$a, 'a', ElementBehavior::DATA_COPY_ASSIGNMENT);
        $clonedEl = $el->cloneAsElement();
        $a->prop1 = '2';
        $elA = $el->getData();
        $cloneA = $clonedEl->getData();
        $this->assertEquals($elA->prop1, $cloneA->prop1, 'Element->clone with copy-assignment behavior configuration must copy object-ref to data.');
    }

    public function testCloneAsElementWithDataCloneWillCloneDataAgain(): void
    {
        $a = new class() {
            public string $val = '1';
        };
        $el = new GenericTypedElement($a::class, $a,'a', ElementBehavior::DATA_CLONE_WHERE_POSSIBLE);
        $clonedEl = $el->cloneAsElement();
        $elA = $el->getData();
        $elA->val = '2';
        $cloneA = $clonedEl->getData();
        $this->assertNotEquals($elA->val, $cloneA->val, 'Element->clone with clone-assignment behavior configuration must not copy object-ref for cloneable data.');
    }

    public function testCloneAsElementWithWeakRef(): void
    {
        $a = new \stdClass();
        $el = new GenericTypedElement($a::class, $a,'a', ElementBehavior::DATA_COPY_WITH_WEAKREF);
        $clonedEl = $el->cloneAsElement();
        unset($a);
        $cloneA = $clonedEl->getData();
        $this->assertNull($cloneA, 'Element->clone with weak-ref behavior configuration must have null data after unsetting referenced value.');
    }


}
