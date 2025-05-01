<?php

namespace PhpSpec\Mock\Matcher;

use PhpSpec\Mock\Matcher\Expectation\Expectation;

interface ExpectationMatcherInterface extends MethodMatcherInterface
{
    public function expect(CallRecorder $subject, array $args): Expectation;
    public function checkExpectation(Expectation $expectation): void;
}
