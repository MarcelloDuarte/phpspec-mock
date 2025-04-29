<?php

namespace PhpSpec\Mock;

interface DoubledMethod
{
    public function satisfies(string $name, array $arguments): bool;
    public function call(string $name, array $arguments);
}