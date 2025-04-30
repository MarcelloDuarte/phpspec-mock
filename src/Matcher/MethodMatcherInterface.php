<?php

namespace PhpSpec\Mock\Matcher;

interface MethodMatcherInterface extends MatcherInterface
{
    public function supports(string $methodName): bool;
}