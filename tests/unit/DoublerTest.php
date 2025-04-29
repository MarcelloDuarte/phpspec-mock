<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\Doubler;
use PhpSpec\Mock\StubbedMethod;
use PhpSpec\Mock\MockedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use BadMethodCallException;

class DoublerTest extends TestCase
{
    public function testItReturnsStubbedValueWhenStubbedMethodIsCalled()
    {
        $doubler = new Doubler();

        $stubbedMethod = new StubbedMethod('someMethod', [42]);
        $stubbedMethod->willReturn('stubbed value');

        $doubler->addDoubledMethod($stubbedMethod);

        $this->assertSame('stubbed value', $doubler->call('someMethod', [42]));
    }

    public function testItRecordsCallWhenMockedMethodIsCalled()
    {
        $doubler = new Doubler();

        $mockedMethod = new MockedMethod('someMethod', [42]);
        $mockedMethod->shouldBeCalled();

        $doubler->addDoubledMethod($mockedMethod);

        // No return value expected from MockedMethod directly
        $this->assertNull($doubler->call('someMethod', [42]));

        // Now verify that the call was recorded correctly
        $mockedMethod->verify();
    }

    public function testItThrowsExceptionWhenMethodIsNotStubbedOrMocked()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method unknownMethod was not stubbed');

        $doubler = new Doubler();
        $doubler->call('unknownMethod', []);
    }

    public function testItThrowsFromMockedMethodWhenExpectationFails()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "someMethod" to be called at least once');

        $doubler = new Doubler();

        $mockedMethod = new MockedMethod('someMethod', [42]);
        $mockedMethod->shouldBeCalled();

        $doubler->addDoubledMethod($mockedMethod);

        // We do not call it!

        $mockedMethod->verify();
    }
}