<?php

namespace PhpSpec\Mock\Wrapper;

class WrapperRegistry
{
    private array $wrappers = [];

    /**
     * addWrapper(fn($method, $args) => SomeWrapper($method, $args)))
     *
     * @param callable $wrapper
     * @return void
     */
    public function addWrapper(callable $wrapper): void
    {
        $this->wrappers[] = $wrapper;
    }

    public function all(): array
    {
        return $this->wrappers;
    }
}