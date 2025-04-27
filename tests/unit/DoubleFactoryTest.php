<?php

namespace Tests\Phpspec\Mock;

use Phpspec\Mock\Double;
use Phpspec\Mock\DoubledMethod;
use Phpspec\Mock\DoubleFactory;
use Phpspec\Mock\DoubleMode;
use PHPUnit\Framework\TestCase;

class DoubleFactoryTest extends TestCase
{
    function testItCreatesADouble()
    {
        $double = DoubleFactory::create();
        $this->assertInstanceOf(Double::class, $double);
    }

    function testItCreatesADoubleNamedAfterTheDoubled()
    {
        $double = DoubleFactory::create(SomeClass::class);
        $this->assertInstanceOf('PhpspecMock__Tests_PhpSpec_Mock_SomeClass_1', $double);
    }

    function testItExtendsTheClassItIsDoubling()
    {
        $double = DoubleFactory::create(SomeClass::class);
        $this->assertInstanceOf(SomeClass::class, $double);
    }

    function testItImplementsTheInterfaceItIsDoubling()
    {
        $double = DoubleFactory::create(SomeInterface::class);
        $this->assertInstanceOf(SomeInterface::class, $double);
    }

    function testCallingAnyMethodOnGeneratedDoubleReturnsDoubledMethod()
    {
        $double = DoubleFactory::create(SomeClass::class);
        $this->assertInstanceOf(DoubledMethod::class, $double->someMethod(42));
    }

    function testDoubleIsCreatedInConfigMode()
    {
        $double = DoubleFactory::create();
        $this->assertEquals($double->__getMode(), DoubleMode::ConfigurationMode);
    }
}

class SomeClass
{
    public function someMethod(int $sum) { return '';}
}

interface SomeInterface{}