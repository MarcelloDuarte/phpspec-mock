<?php

namespace PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\MatcherRegistry;

final class CompositeDoubledMethod implements DoubledMethod, CallRecorder
{
    private ?MethodMetadata $metadata = null;

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
        // Check if the mock accepts this method (e.g. shouldBeCalled)
        if (method_exists($this->mock, $name)) {
            $this->mock->$name(...$arguments);
            return $this;
        }

        // Check if the stub accepts this method (e.g. willReturn)
        if (method_exists($this->stub, $name)) {
            if ($this->stub->isConfigurationMethod($name)) {
                $this->stub->$name(...$arguments);
                return $this;
            }
        }

        // Last resort: try __call on the mock, in case it's a matcher method
        try {
            $this->mock->__call($name, $arguments);
            return $this;
        } catch (\BadMethodCallException) {
            // fall through
        }

        throw new \RuntimeException("Method $name not handled by CompositeDoubledMethod");
    }

    public function getCalls(): array
    {
        return $this->mock->getCalls();
    }

    public function getMethodName(): string
    {
        return $this->mock->getMethodName();
    }

    public function setMetadata(MethodMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
}
