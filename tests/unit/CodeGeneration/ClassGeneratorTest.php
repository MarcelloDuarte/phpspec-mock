<?php

namespace Tests\Phpspec\Mock\CodeGeneration;

use PHPUnit\Framework\TestCase;
use Phpspec\Mock\CodeGeneration\ClassGenerator;

class ClassGeneratorTest extends TestCase
{
    public function testItGeneratesAClassWithMethods()
    {
        $generator = new ClassGenerator();
        $code = $generator->generate('SomeClass', 'implements SomeInterface', '// some methods');

        $this->assertStringContainsString('class SomeClass implements SomeInterface', $code);
        $this->assertStringContainsString('private \\Phpspec\\Mock\\Doubler $doubler;', $code);
        $this->assertStringContainsString('// some methods', $code);
    }
}