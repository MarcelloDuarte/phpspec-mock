<?php

namespace Tests\PhpSpec\Mock;

use Phpspec\Mock\DoubledMethod;
use Phpspec\Mock\DoubleMode;
use Phpspec\Mock\Doubler;
use PHPUnit\Framework\TestCase;

class DoublerTest extends TestCase
{
    function testItIsCreatedInConfigurationMode()
    {
        $doubler = new Doubler();
        $this->assertEquals(DoubleMode::ConfigurationMode, $doubler->mode);
    }

//    function testItCanWrapDoubleMethods()
//    {
//        $doubler = new Doubler();
//        $doubler->addDoubledMethod(new DoubledMethod('someMethod', []));
//
//    }
}