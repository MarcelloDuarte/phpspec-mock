<?php

namespace PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\MatcherRegistry;
use RuntimeException;

final class CompositeDoubledMethod implements DoubledMethod, CallRecorder
{
    public function __construct(
        private readonly MockedMethod $mock,
        private readonly StubbedMethod $stub
    ) {}

    public function satisfies(string $methodName, array $arguments = []): bool
    {
        return $this->stub->satisfies($methodName, $arguments);
    }

    public function call(string $name, array $arguments): mixed
    {
        $this->mock->recordCall($arguments);
        return $this->stub->call($name, $arguments);
    }

    public function verify(): void
    {
        $this->mock->verify();
    }

    public function registerMatchers(MatcherRegistry $registry): void
    {
        $this->mock->registerMatchers($registry);
    }

    public function __call(string $name, array $arguments): mixed
    {
        try {
            return $this->mock->__call($name, $arguments);
        } catch (\BadMethodCallException) {
            if (method_exists($this->stub, $name)) {
                return $this->stub->$name(...$arguments);
            }
            throw new \RuntimeException("Method $name not handled by CompositeDoubledMethod");
        }
    }

    public function getCalls(): array
    {
        return $this->mock->getCalls();
    }

    public function getMethodName(): string
    {
        return $this->mock->getMethodName();
    }
}
