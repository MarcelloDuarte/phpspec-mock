<?php

namespace Tests\PhpSpec\Mock\CodeGeneration;

use PhpSpec\Mock\CodeGeneration\ParametersGenerator;
use PHPUnit\Framework\TestCase;
use PhpSpec\Mock\CodeGeneration\MethodGenerator;

class MethodGeneratorTest extends TestCase
{
    private MethodGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new MethodGenerator(new ParametersGenerator());
    }

    public function testItGeneratesMethodFromReflection()
    {
        $reflection = new \ReflectionMethod(SomeOtherClass::class, 'simpleMethod');
        $body = 'return "stubbed";';

        [$_, $generated] = $this->generator->generate($reflection, $body);

        $this->assertStringContainsString('public function simpleMethod()', $generated);
        $this->assertStringContainsString($body, $generated);
    }

    public function testItAddsReturnTypeIfExists()
    {
        $reflection = new \ReflectionMethod(SomeOtherClass::class, 'methodWithReturnType');
        $body = 'return 42;';

        [$_, $generated] = $this->generator->generate($reflection, $body);

        $this->assertStringContainsString('public function methodWithReturnType(): int', $generated);
        $this->assertStringContainsString($body, $generated);
    }

    public function testItHandlesVoidReturnType()
    {
        $reflection = new \ReflectionMethod(SomeOtherClass::class, 'voidMethod');
        $body = '$this->doSomething();';

        [$_, $generated] = $this->generator->generate($reflection, $body);

        $this->assertStringContainsString('public function voidMethod(): void', $generated);
        $this->assertStringContainsString($body, $generated);
    }
}

// Test helper class:
class SomeOtherClass
{
    public function simpleMethod() {}
    public function methodWithReturnType(): int { return 1; }
    public function voidMethod(): void {}
}