<?php

namespace PhpSpec\Mock\Matcher;

use RuntimeException;

final class ShouldNotBeCalledMatcher implements ExpectationMatcherInterface
{
    public function supports(string $methodName): bool
    {
        return $methodName === 'shouldNotBeCalled';
    }

    public function expect(CallRecorder $subject, array $args): Expectation
    {
        return new Expectation($subject);
    }

    public function checkExpectation(Expectation $expectation): void
    {
        $subject = $expectation->getSubject();
        $calls = $subject->getCalls();

        if (count($calls) > 0) {
            throw new RuntimeException(sprintf(
                'Expected method "%s" not to be called, but it was called %s.',
                $subject->getMethodName(),
                match(count($calls)) { 1 => 'once', default => count($calls) . ' times' }
            ));
        }
    }
}
