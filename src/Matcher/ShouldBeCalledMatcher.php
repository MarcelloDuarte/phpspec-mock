<?php

namespace PhpSpec\Mock\Matcher;

use RuntimeException;

class ShouldBeCalledMatcher implements ExpectationMatcherInterface
{
    public function expect(CallRecorder $subject, array $args): Expectation
    {
        $times = $args[0] ?? null;

        if ($times !== null && !is_int($times)) {
            throw new \InvalidArgumentException("Expected call count must be an integer or null.");
        }

        return new Expectation($subject, [], $times);
    }

    public function checkExpectation(Expectation $expectation): void
    {
        $subject = $expectation->getSubject();
        $calls = $subject->getCalls();
        $expectedCount = $expectation->getExpectedTimes();

        $actualCount = count($calls);

        $times = match ($expectedCount) {
            null, 1 => 'once',
            2 => 'twice',
            3 => 'thrice',
            default => $expectedCount . ' times'
        };

        if ($expectedCount === null) {
            if ($actualCount > 0) {
                return;
            }
            throw new RuntimeException(
                "Expected method \"{$subject->getMethodName()}\" to be called at least {$times}, but it was not."
            );
        }

        if ($actualCount !== $expectedCount) {
            throw new RuntimeException(sprintf(
                'Expected method "'. $subject->getMethodName(). '" to be called %d times, but was called %d times.',
                $expectedCount,
                $actualCount
            ));
        }
    }

    public function supports(string $methodName): bool
    {
        return $methodName === 'shouldBeCalled';
    }
}
