<?php

namespace Tests\Phpspec\Mock;

use Phpspec\Mock\DoubleConfiguration;
use Phpspec\Mock\Double;
use PHPUnit\Framework\TestCase;

class DoubleTest extends TestCase
{
    function testItCreatesADoubleConfigurationFromAnonymousClass()
    {
        $double = Double::create();
        $this->assertInstanceOf(DoubleConfiguration::class, $double);
    }

    function testItCreatesADoubleConfigurationFromClass()
    {
        $double = Double::create(SomeClass::class);
        $this->assertInstanceOf(DoubleConfiguration::class, $double);
    }

    function testItExtendsTheClassItIsDoubling()
    {
        $double = Double::create(SomeClass::class);
        $this->assertInstanceOf(SomeClass::class, $double->stub());
    }

    function testItImplementsTheInterfaceItIsDoubling()
    {
        $double = Double::create(SomeInterface::class);
        $this->assertInstanceOf(SomeInterface::class, $double->stub());
    }
}

class SomeClass
{
    public function someMethod(int $sum): string { return ''; }
}

interface SomeInterface {}