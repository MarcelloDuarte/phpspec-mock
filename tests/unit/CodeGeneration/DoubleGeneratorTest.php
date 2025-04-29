<?php

namespace Tests\PhpSpec\Mock\CodeGeneration;

use PhpSpec\Mock\CollaboratorClassDoesNotExistException;
use PHPUnit\Framework\TestCase;
use PhpSpec\Mock\CodeGeneration\DoubleGenerator;

class DoubleGeneratorTest extends TestCase
{
    public function testItGeneratesFullDoubleClass()
    {
        $generator = new DoubleGenerator();
        [$code, $className] = $generator->generate(SomeInterface::class);

        $this->assertStringContainsString("class $className implements Tests\\PhpSpec\\Mock\\CodeGeneration\\SomeInterface", $code);
        $this->assertStringContainsString('public function someMethod()', $code);
    }

    public function testItThrowsIfClassDoesNotExist()
    {
        $generator = new DoubleGenerator();

        $this->expectException(CollaboratorClassDoesNotExistException::class);

        $generator->generate('SomeNonExistentClass');
    }

    public function testItThrowsIfTryingToDoubleInternalPhpClass()
    {
        $generator = new DoubleGenerator();

        $this->expectException(\RuntimeException::class);

        $generator->generate(\ReflectionClass::class);
    }

    public function testItGeneratesMethodWithCorrectSignatureAndBody()
    {
        $generator = new DoubleGenerator();

        [$classCode, $className] = $generator->generate(SomeClass::class);

        $this->assertStringContainsString('public function simpleMethod(int $a, string $b)', $classCode);
        $this->assertStringContainsString('return $this->doubler->call("simpleMethod", [$a, $b]);', $classCode);
    }
}

interface SomeInterface
{
    public function someMethod();
}