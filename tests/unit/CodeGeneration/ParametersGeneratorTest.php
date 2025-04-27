<?php

namespace Tests\Phpspec\Mock\CodeGeneration;

use PHPUnit\Framework\TestCase;
use Phpspec\Mock\CodeGeneration\ParametersGenerator;

class ParametersGeneratorTest extends TestCase
{
    public function testItGeneratesParameters()
    {
        $reflection = new \ReflectionMethod(SomeClass::class, 'method');
        $generator = new ParametersGenerator();

        [$params, $variables] = $generator->generate($reflection->getParameters());

        $this->assertSame('int $a, string $b = \'default\'', $params);
        $this->assertSame(['$a', '$b'], $variables);
    }
}

class SomeClass
{
    public function method(int $a, string $b = 'default') {}
}