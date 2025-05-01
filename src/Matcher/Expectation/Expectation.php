<?php

namespace PhpSpec\Mock\Matcher\Expectation;

use PhpSpec\Mock\Matcher\CallRecorder;

final class Expectation
{
    public function __construct(
        private CallRecorder $subject,
        private array $expectedArgs = [],
        private ?int $times = null,
        private bool $negated = false
    ) {}

    public function getSubject(): CallRecorder
    {
        return $this->subject;
    }

    public function getExpectedArgs(): array
    {
        return $this->expectedArgs;
    }

    public function getExpectedTimes(): ?int
    {
        return $this->times;
    }

    public function isNegated()
    {
        return $this->negated;
    }
}
