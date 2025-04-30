<?php

namespace PhpSpec\Mock\Matcher;

interface ExpectationMatcherInterface extends MethodMatcherInterface
{
    public function expect(CallRecorder $subject, array $args): Expectation;
    public function checkExpectation(Expectation $expectation): void;
}
