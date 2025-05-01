<?php

namespace PhpSpec\Mock\Matcher\Registry;

use PhpSpec\Mock\Matcher\MatcherInterface;

final class MatcherRegistry
{
    private array $matchers = [];

    public function addMatcher(string $type, MatcherInterface $matcher): void
    {
        $this->matchers[$type][] = $matcher;
    }

    public function all(): array
    {
        return $this->matchers;
    }

    public function getForType(string $type): MatcherRegistry
    {
        $registry = new MatcherRegistry();
        foreach ($this->matchers as $key => $matcher) {
            if ($key === $type) {
                $registry->addMatcher($type, $matcher);
            }
        }

        return $registry;
    }
}
