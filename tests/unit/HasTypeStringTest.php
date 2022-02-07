<?php
declare(strict_types=1);

namespace MBauer\PhpSets\test\unit;

use MBauer\PhpSets\implementations\HasTypeString;
use PHPUnit\Framework\TestCase;

class HasTypeStringTest extends TestCase
{
    protected $mock;

    public function setUp(): void
    {
        $this->mock = new class() {
            use HasTypeString;
            public function __construct()
            {
                $this->type = 'testType';
            }
        };
    }

    public function testGetType(): void
    {
        $expected = 'testType';
        $actual = $this->mock->getType();
        $this->assertEquals($expected, $actual, 'Return value of getType() must be identical to given type.');
    }

    public function testTypeStringIsPubliclyReadable(): void
    {
        try {
            $actual = $this->mock->type;
        } catch(\Throwable $t) {
            $this->fail('Property \'type\' must be publicly readable.');
        }
        $this->assertEquals('testType', $actual, 'Accessed value must be equal to set value.');
    }

    public function testTypeStringIsNotPubliclyWritable(): void
    {
        try {
            $this->mock->type = 'newType';
            $this->fail('Must not be able to set type property.');
        } catch(\Throwable $t) {
            $this->assertTrue(true);
        }
    }
}
