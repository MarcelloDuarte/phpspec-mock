<?php

namespace PhpSpec\Mock;

use Exception;

final class StubbedMethod implements DoubledMethod
{
    private array $stubs = [];
    private null|Exception|string $exceptionToThrow = null;

    public function __construct(private string $name, private array $arguments = [])
    {}

    public function willReturn(...$returnValues): void
    {
        $this->stubs = $returnValues;
    }

    public function willThrow(Exception|string $exception): void
    {
        $this->exceptionToThrow = $exception;
    }

    public function satisfies(string $methodName, array $arguments = []): bool
    {
        return $methodName === $this->name && $arguments == $this->arguments;
    }

    public function hasStubs(): bool
    {
        return $this->stubs !== [];
    }

    public function stubbedValue(): mixed
    {
        return current($this->stubs);
    }

    public function isThrowing(): bool
    {
        return $this->exceptionToThrow !== null;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function throwException(): void
    {
        if ($this->exceptionToThrow instanceof Exception) {
            throw $this->exceptionToThrow;
        } else {
            throw new $this->exceptionToThrow;
        }
    }

    public function call(string $name, array $arguments)
    {
        if ($this->isThrowing()) {
            $this->throwException();
        }

        if ($this->hasStubs()) {
            return $this->stubbedValue();
        }

        return null;
    }
}
