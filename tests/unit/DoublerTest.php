<?php

namespace Tests\PhpSpec\Mock;

use BadMethodCallException;
use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Double;
use PhpSpec\Mock\Doubler;
use PhpSpec\Mock\Matcher\MatcherRegistry;
use PhpSpec\Mock\Matcher\ShouldBeCalledMatcher;
use PhpSpec\Mock\Wrapper\MockedMethod;
use PhpSpec\Mock\Wrapper\StubbedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DoublerTest extends TestCase
{
    public function testItReturnsStubbedValueWhenStubbedMethodIsCalled()
    {
        $doubler = new Doubler();

        $stubbedMethod = new StubbedMethod('someMethod', [42]);
        $stubbedMethod->willReturn('stubbed value');

        $doubler->addDoubledMethod($stubbedMethod, new MethodMetadata('someMethod', 'string'));

        $this->assertSame('stubbed value', $doubler->call('someMethod', [42]));
    }

    public function testItRecordsCallWhenMockedMethodIsCalled()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(new ShouldBeCalledMatcher());

        $mockedMethod = new MockedMethod('someMethod', [42]);
        $mockedMethod->registerMatchers($registry);

        $doubler = new Doubler();

        $mockedMethod->shouldBeCalled();
        $doubler->addDoubledMethod($mockedMethod, new MethodMetadata('someMethod', 'string'));

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
        $this->expectExceptionMessage('Expected method "someMethod" to be called at least once, but it was not.');

        $doubler = new Doubler();

        $registry = new MatcherRegistry();
        $registry->addMatcher(new ShouldBeCalledMatcher());

        $mockedMethod = new MockedMethod('someMethod', [42]);
        $mockedMethod->registerMatchers($registry);

        $mockedMethod->shouldBeCalled();

        $doubler->addDoubledMethod($mockedMethod, new MethodMetadata('someMethod', 'string'));

        // We do not call it!

        $mockedMethod->verify();
    }

    public function testStubbingAndMockingDesyncCausesVerificationFailure()
    {
        $double = Double::create(SomeService::class);

        // Configure expectation and stub value
        $double->someMethod(42)->shouldBeCalled();
        $double->someMethod(42)->willReturn('ok');

        // Use the double
        $service = $double->stub();
        $result = $service->someMethod(42);

        $this->assertSame('ok', $result);

        $double->verify();
    }
}

class SomeService {
    public function someMethod($x): string {
        return 'ok';
    }
}