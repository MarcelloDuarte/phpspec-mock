<?php

namespace PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;

interface Matchable
{
    public function registerMatchers(MatcherRegistry $registry): void;
    public function verify(): void;
}