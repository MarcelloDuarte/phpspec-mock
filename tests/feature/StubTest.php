<?php

namespace Tests\Feature\PhpSpec\Mock;

use PhpSpec\Mock\CodeGeneration\MethodGenerator;
use PhpSpec\Mock\CodeGeneration\ParametersGenerator;
use PhpSpec\Mock\Double;
use PhpSpec\Mock\Wildcard\Argument;
use PHPUnit\Framework\TestCase;

class StubTest extends TestCase
{
    public function testCanStubAMethod()
    {
        $parameterGenerator = Double::create(ParametersGenerator::class);
        $parameterGenerator->generate(Argument::any())
            ->willReturn('int $a');


        $methodGenerator = new MethodGenerator($parameterGenerator->stub());
        $method = new \ReflectionMethod(new SomeClass(), 'someMethod');

        [$metadata, $code] = $methodGenerator->generate($method, 'return "Hello, world";');

        $this->assertSame('    #[\ReturnTypeWillChange]
    public function someMethod(int $a)
    {
        return "Hello, world";
    }
', $code);
    }
}

class SomeClass {
    public function someMethod(int $a) {}
}