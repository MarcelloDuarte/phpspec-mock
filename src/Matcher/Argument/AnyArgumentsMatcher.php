<?php

namespace PhpSpec\Mock\Matcher\Argument;

use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;

class AnyArgumentsMatcher implements ArgumentMatcherInterface
{
    public function matches(mixed $actual): bool
    {
        return true;
    }

    public function isVariadic(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return '...';
    }
}
