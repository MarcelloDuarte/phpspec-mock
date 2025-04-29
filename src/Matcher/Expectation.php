<?php

namespace PhpSpec\Mock\Matcher;

final class Expectation
{
    public function __construct(
        private CallRecorder $subject,
        private array $expectedArgs = [],
        private ?int $times = null
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
}
