<?php

namespace Tests\PhpSpec\Mock\CodeGeneration;

use PHPUnit\Framework\TestCase;
use PhpSpec\Mock\CodeGeneration\ParametersGenerator;

class ParametersGeneratorTest extends TestCase
{
    private ParametersGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new ParametersGenerator();
    }

    public function testItGeneratesSimpleParameters()
    {
        $reflection = new \ReflectionMethod(SomeClass::class, 'simpleMethod');

        $generated = $this->generator->generate($reflection);

        $this->assertSame('int $a, string $b', $generated);
    }

    public function testItHandlesNullableTypes()
    {
        $reflection = new \ReflectionMethod(SomeClass::class, 'nullableMethod');

        $generated = $this->generator->generate($reflection);

        $this->assertSame('?string $a', $generated);
    }

    public function testItHandlesVariadicParameters()
    {
        $reflection = new \ReflectionMethod(SomeClass::class, 'variadicMethod');

        $generated = $this->generator->generate($reflection);

        $this->assertSame('string ...$args', $generated);
    }

    public function testItHandlesOptionalParameters()
    {
        $reflection = new \ReflectionMethod(SomeClass::class, 'optionalMethod');

        $generated = $this->generator->generate($reflection);

        $this->assertSame('?int $a = null, ?string $b = null', $generated);
    }
}

// Helper class
class SomeClass
{
    public function simpleMethod(int $a, string $b) {}
    public function nullableMethod(?string $a) {}
    public function variadicMethod(string ...$args) {}
    public function optionalMethod(?int $a = null, ?string $b = null) {}
}