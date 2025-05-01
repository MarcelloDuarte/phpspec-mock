<?php

namespace Tests\PhpSpec\Mock\Double;

use PhpSpec\Mock\Double;
use PhpSpec\Mock\Wrapper\DoubledMethod;
use PHPUnit\Framework\TestCase;

class DoubleConfigurationTest extends TestCase
{
    function testCallingAnyMethodOnDoubleConfigurationReturnsDoubledMethod()
    {
        $double = Double::create(SomeOtherClass::class);
        $this->assertInstanceOf(DoubledMethod::class, $double->someMethod(42));
    }
}

class SomeOtherClass
{
    public function someMethod(int $sum): string { return ''; }
}