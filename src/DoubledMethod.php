<?php

namespace Phpspec\Mock;

class DoubledMethod
{
    private array $stubs = [];

    public function __construct(private string $name, private array $arguments = [])
    {}

    public function willReturn(...$returnValues)
    {
        $this->stubs = $returnValues;
    }

    public function satisfies(string $methodName, array $arguments = [])
    {
        return $methodName === $this->name && $arguments == $this->arguments;
    }

    public function stubbedValue()
    {
        return current($this->stubs);
    }
}