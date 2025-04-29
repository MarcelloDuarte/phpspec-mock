<?php

namespace PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Matcher\MatcherRegistry;

interface DoubledMethod
{
    public function satisfies(string $methodName, array $arguments): bool;
    public function call(string $name, array $arguments);
    public function registerMatchers(MatcherRegistry $registry): void;
}