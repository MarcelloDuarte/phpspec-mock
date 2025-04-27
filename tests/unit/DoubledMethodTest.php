<?php

namespace Tests\Phpspec\Mock;

use Phpspec\Mock\Double;
use Phpspec\Mock\DoubledMethod;
use Phpspec\Mock\DoubleMode;
use PHPUnit\Framework\TestCase;

class DoubledMethodTest extends TestCase
{
    function testInstatiation()
    {
        $doubledMethod = new DoubledMethod('someMethod', []);
        $this->assertInstanceOf(DoubledMethod::class, $doubledMethod);
    }

//    function testWillReturnWillCreateAStub()
//    {
//        $doubledMethod = new DoubledMethod('someMethod', []);
//        $doubledMethod->willReturn(42);
//
//        $this->assertSame($doubledMethod->someMethod(), 42);
//    }
}