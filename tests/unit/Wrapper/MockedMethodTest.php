<?php

namespace Tests\PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Matcher\MatcherRegistry;
use PhpSpec\Mock\Matcher\ShouldBeCalledMatcher;
use PhpSpec\Mock\Matcher\ShouldNotBeCalledMatcher;
use PhpSpec\Mock\Wrapper\MockedMethod;
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
        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new ShouldBeCalledMatcher());

        $method = new MockedMethod('foo');
        $method->registerMatchers($registry);

        $method->shouldBeCalled();
        $method->recordCall([]);

        $this->expectNotToPerformAssertions();
        $method->verify();
    }

    public function testItFailsVerificationIfNotCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to be called at least once');

        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new ShouldBeCalledMatcher());

        $method = new MockedMethod('foo');
        $method->registerMatchers($registry);
        $method->shouldBeCalled();
        $method->verify();
    }

    public function testItPassesWhenCalledExactNumberOfTimes()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new ShouldBeCalledMatcher());

        $method = new MockedMethod('foo');
        $method->registerMatchers($registry);

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

        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new ShouldBeCalledMatcher());

        $method = new MockedMethod('foo');
        $method->registerMatchers($registry);
        $method->shouldBeCalled(2);
        $method->recordCall([]); // Only once

        $method->verify();
    }

    public function testItFailsIfCalledWhenShouldNotBeCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" not to be called, but it was called once.');

        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new ShouldNotBeCalledMatcher());

        $method = new MockedMethod('foo');
        $method->registerMatchers($registry);
        $method->shouldNotBeCalled();
        $method->recordCall([]);

        $method->verify();
    }
}
