<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\Argument;
use PhpSpec\Mock\DoubleConfiguration;
use PhpSpec\Mock\Double;
use PhpSpec\Mock\Matcher\ExpectationException;
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

    public function testDiffFailureShowsMismatch()
    {
        $this->expectException(ExpectationException::class);
        $this->expectExceptionMessage('No stubbed value found for method "someMethod()" with arguments:
  Called with: ["bar"]
  Known stubs:
    - someMethod(["foo"])');

        $mock = Double::create(SomeService::class);
        $mock->someMethod(Argument::exact('foo'))->shouldBeCalled();

        $instance = $mock->stub();
        $instance->someMethod('bar'); // wrong argument


        $mock->verify(); // this should fail
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