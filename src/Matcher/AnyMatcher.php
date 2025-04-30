<?php

namespace PhpSpec\Mock\Matcher;

final class AnyMatcher implements ArgumentMatcherInterface
{
    public function matches(mixed $actual): bool
    {
        return true; // Always matches any argument
    }

    public function supports(string $methodName): bool
    {
        return $methodName === 'any';
    }

    public function isVariadic(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return '<any>';
    }
}
