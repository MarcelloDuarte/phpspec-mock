<?php

namespace PhpSpec\Mock\Matcher;

interface ArgumentMatcherInterface extends MatcherInterface
{
    public function matches(mixed $actual): bool;
    public function isVariadic(): bool;
}
