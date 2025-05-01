<?php

namespace Tests\PhpSpec\Mock\Wrapper\DoubledMethod;

use PhpSpec\Mock\Matcher\Method\BeCalledMatcher;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Matcher\Runner\MatcherRunner;
use PhpSpec\Mock\Wrapper\DoubledMethod\MockedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MockedMethodTest extends TestCase
{
    public function testItRecordsCalls()
    {
        $method = new MockedMethod('foo', [], new MatcherRunner());
        $method->recordCall([1, 2]);

        $this->expectNotToPerformAssertions(); // No errors = pass
    }

    public function testItPassesVerificationIfCalled()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new BeCalledMatcher());

        $method = new MockedMethod('foo', [], new MatcherRunner());
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
        $registry->addMatcher(MockedMethod::class, new BeCalledMatcher());

        $method = new MockedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldBeCalled();
        $method->verify();
    }

    public function testItPassesWhenCalledExactNumberOfTimes()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new BeCalledMatcher());

        $method = new MockedMethod('foo', [], new MatcherRunner());
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
        $registry->addMatcher(MockedMethod::class, new BeCalledMatcher());

        $method = new MockedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldBeCalled(2);
        $method->recordCall([]); // Only once

        $method->verify();
    }

    public function testItFailsIfCalledWhenShouldNotBeCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" not to be called, but was called once.');

        $registry = new MatcherRegistry();
        $registry->addMatcher(MockedMethod::class, new BeCalledMatcher());

        $method = new MockedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldNotBeCalled();
        $method->recordCall([]);

        $method->verify();
    }
}
