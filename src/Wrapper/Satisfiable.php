<?php

namespace PhpSpec\Mock\Wrapper;

interface Satisfiable
{
    public function satisfies(string $methodName, array $arguments): bool;
    public function getExpectedArgs(): array;
    public function isConfigurationMethod(string $name): bool;
}