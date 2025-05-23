<?php

namespace PhpSpec\Mock\Matcher\Argument;

use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;

final class ExactMatcher implements ArgumentMatcherInterface
{
    private mixed $expected;

    public function __construct(mixed $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return $this->expected === $actual;
    }

    public function isVariadic(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return json_encode($this->expected);
    }
}