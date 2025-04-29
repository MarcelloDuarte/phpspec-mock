<?php

namespace PhpSpec\Mock\Matcher;

interface ExpectationMatcherInterface extends MatcherInterface
{

    public function expect(CallRecorder $subject, array $args): Expectation;
    public function checkExpectation(Expectation $expectation): void;
}
