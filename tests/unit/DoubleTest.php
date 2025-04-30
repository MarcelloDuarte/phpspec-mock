<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\DoubleConfiguration;
use PhpSpec\Mock\Double;
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

    public function testItCanDoubleReadonlyClass()
    {
        $double = Double::create(ReadonlyClass::class);

        $double->getName()->willReturn('stubbed');

        $instance = $double->stub();

        $this->assertInstanceOf(ReadonlyClass::class, $instance);
        $this->assertSame('stubbed', $instance->getName());
    }
}

class SomeClass
{
    public function someMethod(int $sum): string { return ''; }
}

interface SomeInterface {}

readonly class ReadonlyClass
{
    public function __construct(public string $name = 'default') {}

    public function getName(): string
    {
        return $this->name;
    }
}