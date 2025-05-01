<?php

namespace Tests\Feature\PhpSpec\Mock;

use PhpSpec\Mock\CodeGeneration\ParametersGenerator;
use PhpSpec\Mock\Double;
use PhpSpec\Mock\Wildcard\Argument;
use PHPUnit\Framework\TestCase;

class MockTest extends TestCase
{
    public function testCanMockMethodCall()
    {
        $mock = Double::create(ParametersGenerator::class);

        $mock->generate(Argument::any())->willReturn('foo');

        $instance = $mock->mock();
        $instance->generate(new \ReflectionMethod(new SomeClass(), 'someMethod'));

        $mock->verify();
        $this->expectNotToPerformAssertions();
    }
}