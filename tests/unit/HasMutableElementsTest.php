<?php

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\HasMutableElements;
use PHPUnit\Framework\TestCase;

class HasMutableElementsTest extends TestCase
{
    protected $mock;

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

        $this->mock = new class([$mockEl1, $mockEl2, $mockEl3]) {
            use HasMutableElements;
            public function __construct($mocks)
            {
                $this->elements = ['a' => $mocks[0], 'b' => $mocks[1], '3' => $mocks[2]];
            }
        };
    }


    public function testGetElementIdsContainsAllPresentIds():void
    {
        $result = $this->mock->getElementIds();
        $this->assertContains('a', $result, 'Must return all present ids.');
        $this->assertContains('b', $result, 'Must return all present ids.');
        $this->assertContains('3', $result, 'Must return all present ids.');
    }

    public function testGetElementIdsContainsOnlyPresentIds():void
    {
        $result = $this->mock->getElementIds();
        $expected = ['a','b','3'];
        foreach ($result as $returnedId) {
            $this->assertContains($returnedId, $expected, 'Must only return present ids.');
        }
    }
}
