<?php

namespace PhpSpec\Mock\Matcher\Runner;

use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\Expectation\Expectation;
use PhpSpec\Mock\Matcher\ExpectationMatcherInterface;

final class MatcherRunner
{
    public function apply(
        array $matchers,
        CallRecorder $subject,
        string $method,
        array $args
    ): ?Expectation {
        foreach ($matchers as $matcher) {
            if ($matcher instanceof ExpectationMatcherInterface && $matcher->supports($method)) {
                $isNegated = str_starts_with($method, 'shouldNot');
                return $matcher->expect($subject, $args, $isNegated);
            }
        }

        return null;
    }
}
