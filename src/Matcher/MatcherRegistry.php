<?php

namespace PhpSpec\Mock\Matcher;

final class MatcherRegistry
{
    private array $matchers = [];

    public function addMatcher(MatcherInterface $matcher): void
    {
        $this->matchers[] = $matcher;
    }

    public function all(): array
    {
        return $this->matchers;
    }
}
