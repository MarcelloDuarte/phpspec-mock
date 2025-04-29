<?php

namespace Tests\PhpSpec\Mock\CodeGeneration;

use PHPUnit\Framework\TestCase;
use PhpSpec\Mock\CodeGeneration\ClassGenerator;

class ClassGeneratorTest extends TestCase
{
    public function testItGeneratesAClassWithMethods()
    {
        $generator = new ClassGenerator();
        $code = $generator->generate('SomeClass', 'implements SomeInterface', '// some methods');

        $this->assertStringContainsString('class SomeClass implements SomeInterface', $code);
        $this->assertStringContainsString('private \\PhpSpec\\Mock\\Doubler $doubler;', $code);
        $this->assertStringContainsString('// some methods', $code);
    }
}