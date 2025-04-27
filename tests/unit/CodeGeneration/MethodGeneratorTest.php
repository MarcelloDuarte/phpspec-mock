<?php

namespace Tests\Phpspec\Mock\CodeGeneration;

use PHPUnit\Framework\TestCase;
use Phpspec\Mock\CodeGeneration\MethodGenerator;

class MethodGeneratorTest extends TestCase
{
    public function testItGeneratesAMethod()
    {
        $generator = new MethodGenerator();
        $methodCode = $generator->generate('someMethod', 'int $a, string $b', 'string', 'return "foo";');

        $this->assertStringContainsString('public function someMethod(int $a, string $b): string', $methodCode);
        $this->assertStringContainsString('return "foo";', $methodCode);
    }
}