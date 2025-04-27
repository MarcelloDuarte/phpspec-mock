<?php

namespace Tests\Phpspec\Mock;

use Phpspec\Mock\DoubledMethod;
use PHPUnit\Framework\TestCase;
use Phpspec\Mock\Double;

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