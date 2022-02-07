<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\GenericElement;
use MBauer\PhpSets\implementations\GenericTypedElement;
use MBauer\PhpSets\implementations\ProvidesTypedElements;
use PHPUnit\Framework\TestCase;


class ProvidesTypedElementsTest extends TestCase
{
    protected $mock;
    protected $mockElBuilder;
    protected $mockEls;

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
        $this->mockEls = [$mockEl1, $mockEl2, $mockEl3, $mockEl4];

        $this->mock = new class([$mockEl1, $mockEl2, $mockEl3]) {
            use ProvidesTypedElements;
            protected readonly string $type;
            public function __construct($mocks)
            {
                $this->type = 'string';
                $this->elements = ['1' => $mocks[0], '2' => $mocks[1], '3' => $mocks[2]];
            }
        };
    }

    public function testGetElementByIdSuccessOnPresent(): void
    {
        $mockEl1 = $this->mockEls[0];
        $this->assertSame($mockEl1, $this->mock->getElementById('1'), 'Method must return same instance for present element.');
    }

    public function testGetElementByIdFailureOnNotPresent(): void
    {
        $this->assertNull($this->mock->getElementById('4'), 'Method must return null when queried for non-present element.');
    }

}
