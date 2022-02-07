<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\ProvidesTypedElementCheck;
use PHPUnit\Framework\TestCase;


class ProvidesTypedElementCheckTest extends TestCase
{
    protected $mock;
    protected $mockElBuilder;
    protected $mockEls;

    protected function setUp(): void
    {
        $this->mockElBuilder = $this->getMockBuilder(GenericTypedElement::class)->enableOriginalConstructor();
        $mockEl1 = $this->mockElBuilder->setConstructorArgs(['string','','a'])->getMock();
        $mockEl1->expects($this->any())->method('getIdentifier')->willReturn('a');
        $mockEl2 = $this->mockElBuilder->setConstructorArgs(['string','','b'])->getMock();
        $mockEl2->expects($this->any())->method('getIdentifier')->willReturn('b');
        $mockEl3 = $this->mockElBuilder->setConstructorArgs(['string','','3'])->getMock();
        $mockEl3->expects($this->any())->method('getIdentifier')->willReturn('3');
        $mockEl4 = $this->mockElBuilder->setConstructorArgs(['string','','d'])->getMock();
        $mockEl4->expects($this->any())->method('getIdentifier')->willReturn('d');
        $this->mockEls = [$mockEl1, $mockEl2, $mockEl3, $mockEl4];

        $this->mock = new class([$mockEl1, $mockEl2, $mockEl3]) {
            use ProvidesTypedElementCheck;
            public readonly string $type;
            public function __construct($mocks)
            {
                $this->type = 'string';
                $this->elements = ['a' => $mocks[0], 'b' => $mocks[1], '3' => $mocks[2]];
            }
        };
    }


    public function testHasElementByIdSuccessOnPresent(): void
    {
        $this->assertTrue($this->mock->hasElementById('a'));
    }
    public function testHasElementByIdFailureOnNotPresent(): void
    {
        $this->assertFalse($this->mock->hasElementById('d'));
    }

    public function testHasElementSuccessOnPresent(): void
    {
        $mockEl1 = $this->mockEls[0];
        $this->assertTrue($this->mock->hasElement($mockEl1));
    }
    public function testHasElementFailureOnNotPresent(): void
    {
        $mockEl4 = $this->mockEls[3];
        $this->assertFalse($this->mock->hasElement($mockEl4));
    }

}
