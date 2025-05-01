<?php

namespace Tests\PhpSpec\Mock\Wrapper\DoubledMethod;

use PhpSpec\Mock\Matcher\Method\HaveBeenCalledMatcher;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Matcher\Runner\MatcherRunner;
use PhpSpec\Mock\Wrapper\DoubledMethod\SpiedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SpiedMethodTest extends TestCase
{
    public function testItRecordsCalls()
    {
        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->recordCall([1, 2]);

        $this->expectNotToPerformAssertions(); // No errors = pass
    }

    public function testItPassesVerificationIfCalled()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());

        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);

        $method->shouldHaveBeenCalled();
        $method->recordCall([]);

        $this->expectNotToPerformAssertions();
        $method->verify();
    }

    public function testItFailsVerificationIfNotCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to have been called at least once');

        $registry = new MatcherRegistry();
        $registry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());

        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldHaveBeenCalled();
        $method->verify();
    }

    public function testItPassesWhenCalledExactNumberOfTimes()
    {
        $registry = new MatcherRegistry();
        $registry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());

        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);

        $method->shouldHaveBeenCalled(2);
        $method->recordCall([]);
        $method->recordCall([]);

        $this->expectNotToPerformAssertions();
        $method->verify();
    }

    public function testItFailsWhenNotCalledExactNumberOfTimes()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" to have been called 2 times');

        $registry = new MatcherRegistry();
        $registry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());

        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldHaveBeenCalled(2);
        $method->recordCall([]); // Only once

        $method->verify();
    }

    public function testItFailsIfCalledWhenShouldNotHaveBeenCalled()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected method "foo" not to have been called, but was called once.');

        $registry = new MatcherRegistry();
        $registry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());

        $method = new SpiedMethod('foo', [], new MatcherRunner());
        $method->registerMatchers($registry);
        $method->shouldNotHaveBeenCalled();
        $method->recordCall([]);

        $method->verify();
    }
}
