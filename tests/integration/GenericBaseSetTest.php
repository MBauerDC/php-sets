<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\integration;

use MBauer\PhpSets\contracts\Element;
use MBauer\PhpSets\implementations\GenericBaseSet;
use MBauer\PhpSets\implementations\GenericElement;
use MBauer\PhpSets\test\unit\Pure;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use function count;

class GenericBaseSetTest extends TestCase
{

    protected MockBuilder $genericElMockBuilder;

    public function setUp(): void
    {
        $this->genericElMockBuilder = $this->getMockBuilder(GenericElement::class);
    }

    public function testToArray(): void
    {
        // For mocks the methods will no longer return specified value after clone
        $el1 = new GenericElement('a', 'a');
        $el2 = new GenericElement('b', 'b');
        $el3 = new GenericElement('c','c');

        $set = new class($el1, $el2, $el3) extends GenericBaseSet {

            #[Pure] public function getElementById(string $id): ?Element
            {
                return $this->elements[$id] ?? null;
            }
        };
        $actual = [];
        $asArr = $set->toArray();
        foreach ($asArr as $id => $el) {
            $actual[$id] = $el->getIdentifier();
        }

        $expected = ['a' => $el1->getIdentifier(), 'b' => $el2->getIdentifier(), 'c' => $el3->getIdentifier()];

        $this->assertSame($expected, $actual, 'GenericBaseSet\'s toArray method must return the given elements wit their ids as keys.');

    }

    public function testCount(): void
    {

        $el1 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el1->expects($this->any())->method('getIdentifier')->willReturn('a');

        $el2 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el2->expects($this->any())->method('getIdentifier')->willReturn('b');

        $el3 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el3->expects($this->any())->method('getIdentifier')->willReturn('c');

        $expected = 3;

        $set = new class($el1, $el2, $el3) extends GenericBaseSet {

            #[Pure] public function getElementById(string $id): ?Element
            {
                return $this->elements[$id] ?? null;
            }
        };
        $this->assertEquals($expected, count($set), 'Set count must be accurate.');
    }

    public function testGetIterator(): void
    {
        $expected = [];

        $id1 = 'a';
        $el1 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el1->expects($this->any())->method('getIdentifier')->willReturn($id1);

        $id2 = 'b';
        $el2 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el2->expects($this->any())->method('getIdentifier')->willReturn($id2);

        $id3 = 'c';
        $el3 = $this->genericElMockBuilder->disableOriginalConstructor()->getMock();
        $el3->expects($this->any())->method('getIdentifier')->willReturn($id3);
        $idsEls = [$id1 => $el1, $id2 => $el2, $id3 => $el3];

        $set = new class($el1, $el2, $el3) extends GenericBaseSet {

            #[Pure] public function getElementById(string $id): ?Element
            {
                return $this->elements[$id] ?? null;
            }
        };
        $actual = [];
        foreach ($set as $id => $el) {
            $this->assertArrayHasKey($id, $idsEls, 'Iterator must use element-ids as keys');
            $arrVal = $idsEls[$id] ?? null;
            $this->assertSame($el, $arrVal, 'Iterator must have correct Elements for keys.');
        }
    }
}
