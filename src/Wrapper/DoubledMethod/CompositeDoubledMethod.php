<?php

namespace PhpSpec\Mock\Wrapper\DoubledMethod;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Wrapper\DoubledMethod;
use PhpSpec\Mock\Wrapper\Matchable;
use PhpSpec\Mock\Wrapper\Satisfiable;

final class CompositeDoubledMethod implements DoubledMethod
{
    private ?MethodMetadata $metadata = null;
    private readonly array $doubleMethods;

    public function __construct(DoubledMethod ...$doubleMethods)
    {
        $this->doubleMethods = $doubleMethods;
    }

    public function satisfies(string $methodName, array $arguments = []): bool
    {
        $satisfied = false;
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof Satisfiable) {
                $satisfied = $doubleMethod->satisfies($methodName, $arguments);
            }
        }

        return $satisfied;
    }

    public function call(string $name, array $arguments): mixed
    {
        $result = null;
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof CallRecorder) {
                $doubleMethod->recordCall($arguments);
            }

            if ($doubleMethod instanceof Satisfiable && $doubleMethod->satisfies($name, $arguments)) {
                return $doubleMethod->call($name, $arguments);
            }
        }

        throw new \RuntimeException("No stubbed return value available for method '$name'");
    }

    public function verify(): void
    {
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof Matchable) {
                $doubleMethod->verify();
            }
        }
    }

    public function registerMatchers(MatcherRegistry $registry): void
    {
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof Matchable) {
                $doubleMethod->registerMatchers($registry->getForType(get_class($doubleMethod)));
            }
        }
    }

    public function __call(string $name, array $arguments): mixed
    {
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof CallRecorder && method_exists($doubleMethod, $name)) {
                $doubleMethod->$name(...$arguments);
                return $this;
            }
        }

        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof Satisfiable && $doubleMethod->isConfigurationMethod($name)) {
                $doubleMethod->$name(...$arguments);
                return $this;
            }
        }

        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof CallRecorder) {
                try {
                    $doubleMethod->__call($name, $arguments);
                    return $this;
                } catch (\BadMethodCallException) {
                    // try next
                }
            }
        }

        throw new \RuntimeException("Method $name not handled by CompositeDoubledMethod");
    }

    public function getCalls(): array
    {
        $calls = [];
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof CallRecorder) {
                $calls = $doubleMethod->getCalls();
            }
        }
        return $calls;
    }

    public function getMethodName(): string
    {
        $methodName = '';
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof CallRecorder) {
                $methodName =  $doubleMethod->getMethodName();
            }
        }
        return $methodName;
    }

    public function setMetadata(MethodMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getExpectedArgs(): array
    {
        foreach ($this->doubleMethods as $doubleMethod) {
            if ($doubleMethod instanceof Satisfiable) {
                return $doubleMethod->getExpectedArgs();
            }
        }
        return [];
    }
}
