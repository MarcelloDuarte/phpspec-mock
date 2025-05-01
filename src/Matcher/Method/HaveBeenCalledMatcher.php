<?php

namespace PhpSpec\Mock\Matcher\Method;

use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\Expectation\Expectation;
use PhpSpec\Mock\Matcher\ExpectationMatcherInterface;
use RuntimeException;

class HaveBeenCalledMatcher implements ExpectationMatcherInterface
{
    public function supports(string $methodName): bool
    {
        return in_array($methodName, ['shouldHaveBeenCalled', 'shouldNotHaveBeenCalled'], true);
    }

    public function expect(CallRecorder $subject, array $args, bool $isNegated = false): Expectation
    {
        $times = $args[0] ?? null;

        if ($times !== null && !is_int($times)) {
            throw new \InvalidArgumentException("Expected call count must be an integer or null.");
        }

        return new Expectation($subject, [], $times, $isNegated);
    }

    public function checkExpectation(Expectation $expectation): void
    {
        $calls = $expectation->getSubject()->getCalls();
        $expected = $expectation->getExpectedTimes();
        $negated = $expectation->isNegated();

        $count = count($calls);
        $actual = $count === 1 ? 'once' : $count . ' times';

        if ($negated && $count > 0) {
            throw new RuntimeException("Expected method \"{$expectation->getSubject()->getMethodName()}\" not to have been called, but was called $actual.");
        }

        if (!$negated && $expected !== null && $count !== $expected) {
            throw new RuntimeException("Expected method \"{$expectation->getSubject()->getMethodName()}\" to have been called $expected times, but was called $actual.");
        }

        if (!$negated && $expected === null && $count === 0) {
            throw new RuntimeException("Expected method \"{$expectation->getSubject()->getMethodName()}\" to have been called at least once, but it was not.");
        }
    }
}