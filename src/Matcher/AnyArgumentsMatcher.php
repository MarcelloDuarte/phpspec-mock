<?php

namespace PhpSpec\Mock\Matcher;

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
