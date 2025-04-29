<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\StubbedMethod;
use PHPUnit\Framework\TestCase;
use PhpSpec\Mock\Double;

class DoubleConfigurationTest extends TestCase
{
    function testCallingAnyMethodOnDoubleConfigurationReturnsDoubledMethod()
    {
        $double = Double::create(SomeOtherClass::class);
        $this->assertInstanceOf(StubbedMethod::class, $double->someMethod(42));
    }
}

class SomeOtherClass
{
    public function someMethod(int $sum): string { return ''; }
}