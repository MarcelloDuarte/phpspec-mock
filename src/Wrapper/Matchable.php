<?php

namespace PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Matcher\MatcherRegistry;

interface Matchable
{
    public function registerMatchers(MatcherRegistry $registry): void;
    public function verify(): void;
}