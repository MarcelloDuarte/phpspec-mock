<?php

namespace Tests\Phpspec\Mock\CodeGeneration;

use Phpspec\Mock\CollaboratorClassDoesNotExistException;
use PHPUnit\Framework\TestCase;
use Phpspec\Mock\CodeGeneration\DoubleGenerator;

class DoubleGeneratorTest extends TestCase
{
    public function testItGeneratesFullDoubleClass()
    {
        $generator = new DoubleGenerator();
        [$code, $className] = $generator->generate(SomeInterface::class);

        $this->assertStringContainsString("class $className implements Tests\\Phpspec\\Mock\\CodeGeneration\\SomeInterface", $code);
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
}

interface SomeInterface
{
    public function someMethod();
}