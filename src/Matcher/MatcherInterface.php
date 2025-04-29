<?php

namespace PhpSpec\Mock\Matcher;

interface MatcherInterface
{
    public function supports(string $methodName): bool;
}
