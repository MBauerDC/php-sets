<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\CanMutateTypedElements;
use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\HasMutableElements;
use PHPUnit\Framework\TestCase;

class CanMutateTypedElementsTest extends TestCase
{
    protected $mock;
    protected $mockEls;
    protected array $elementsRef;

    protected function setUp(): void
    {
        $this->mockElBuilder = $this->getMockBuilder(GenericTypedElement::class);
        $mockEl1 = $this->mockElBuilder->setConstructorArgs(['string','','a'])->getMock();
        $mockEl1->expects($this->any())->method('getIdentifier')->willReturn('a');
        $mockEl2 = $this->mockElBuilder->setConstructorArgs(['string','','b'])->getMock();
        $mockEl2->expects($this->any())->method('getIdentifier')->willReturn('b');
        $mockEl3 = $this->mockElBuilder->setConstructorArgs(['string','','3'])->getMock();
        $mockEl3->expects($this->any())->method('getIdentifier')->willReturn('3');
        $mockEl4 = $this->mockElBuilder->setConstructorArgs(['string','','d'])->getMock();
        $mockEl4->expects($this->any())->method('getIdentifier')->willReturn('d');
        $mockEl5 = $this->mockElBuilder->setConstructorArgs(['string','','e'])->getMock();
        $mockEl5->expects($this->any())->method('getIdentifier')->willReturn('e');
        $mockEl6 = $this->mockElBuilder->setConstructorArgs(['string','','f'])->getMock();
        $mockEl6->expects($this->any())->method('getIdentifier')->willReturn('f');
        $this->mockEls = [$mockEl1, $mockEl2, $mockEl3, $mockEl4, $mockEl5, $mockEl6];

        $this->elementsRef = [];
        $refSetter = fn(array &$els) => $this->elementsRef = &$els;
        $this->mock = new class([$mockEl1, $mockEl2, $mockEl3], $refSetter) {
            use HasMutableElements, CanMutateTypedElements;
            public function __construct($mocks, $refSetter)
            {
                $this->elements = ['a' => $mocks[0], 'b' => $mocks[1], '3' => $mocks[2]];
                $refSetter($this->elements);
            }
        };
    }

    public function testEmptyAddMakesNoChanges(): void
    {
        $expected = [...$this->elementsRef];
        $this->mock->addElements();
        $actual = [...$this->elementsRef];
        $this->assertSame($expected, $actual, 'Empty call to AddElements must not change state');
    }

    public function testAddSingleElement(): void
    {
        $el4 = $this->mockEls[3];
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore + [$el4->getIdentifier() => $el4];
        $this->mock->addElements($el4);
        $actual = [...$this->elementsRef];
        $this->assertSame($expected, $actual, 'Adding a single element must create a new array entry with proper key.');
    }

    public function testAddMultipleElements(): void
    {
        $el5 = $this->mockEls[4];
        $el6 = $this->mockEls[5];
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore + [$el5->getIdentifier() => $el5, $el6->getIdentifier() => $el6];
        $this->mock->addElements($el5, $el6);
        $actual = [...$this->elementsRef];
        $this->assertSame($expected, $actual, 'Adding a single element must create a new array entry with proper key.');
    }

    public function testRemoveSingleElementById(): void
    {
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore;
        unset($expected['a']);
        $this->mock->removeElementsById('a');
        $actual = [...$this->elementsRef];
        $this->mock->addElements($this->mockEls[0]);
        $this->assertSame($expected, $actual, 'Removing single element by id must unset the correct array entry.');

    }

    public function testRemoveMultipleElementsById(): void
    {
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore;
        unset($expected['a'], $expected['b']);
        $this->mock->removeElementsById('a', 'b');
        $actual = [...$this->elementsRef];
        $this->mock->addElements($this->mockEls[0], $this->mockEls[1]);
        $this->assertSame($expected, $actual, 'Removing multiple elements by id must unset the correct array entries.');
    }

    public function testRemoveSingleElement(): void
    {
        $el1 = $this->mockEls[0];
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore;
        unset($expected['a']);
        $this->mock->removeElements($el1);
        $actual = [...$this->elementsRef];
        $this->mock->addElements($el1);
        $this->assertSame($expected, $actual, 'Removing single element must unset the correct array entry.');
    }

    public function testRemoveMultipleElements(): void
    {
        $el1 = $this->mockEls[0];
        $el2 = $this->mockEls[1];
        $elsBefore = [...$this->elementsRef];
        $expected = $elsBefore;
        unset($expected['a'], $expected['b']);
        $this->mock->removeElements($el1, $el2);
        $actual = [...$this->elementsRef];
        $this->mock->addElements($el1, $el2);
        $this->assertSame($expected, $actual, 'Removing multiple elements must unset the correct array entries.');
    }
}
