<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\MockedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MockedMethodTest extends TestCase
{
    public function testItRecordsCalls()
    {
        $method = new MockedMethod('foo');
        $method->recordCall([1, 2]);

        $this->expectNotToPerformAssertions(); // No errors = pass
    }

    public function testItPassesVerificationIfCalled()
    {
        $method = new MockedMethod('foo');
        $method->shouldBeCalled();
        $method->recordCall([]);

        $this->expectNotToPerformAssertions();
        $method->verify();
    }

    public function testItFailsVerificationIfNotCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to be called at least once');

        $method = new MockedMethod('foo');
        $method->shouldBeCalled();
        $method->verify();
    }

    public function testItPassesWhenCalledExactNumberOfTimes()
    {
        $method = new MockedMethod('foo');
        $method->shouldBeCalled(2);
        $method->recordCall([]);
        $method->recordCall([]);

        $this->expectNotToPerformAssertions();
        $method->verify();
    }

    public function testItFailsWhenNotCalledExactNumberOfTimes()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to be called 2 times');

        $method = new MockedMethod('foo');
        $method->shouldBeCalled(2);
        $method->recordCall([]); // Only once

        $method->verify();
    }

    public function testItFailsIfCalledWhenShouldNotBeCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to be called 0 times, but was called 1 times.');

        $method = new MockedMethod('foo');
        $method->shouldNotBeCalled();
        $method->recordCall([]);

        $method->verify();
    }
}
