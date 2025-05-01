<?php

namespace PhpSpec\Mock\Wrapper;

interface ProxyCaller
{
    public function call(string $name, array $arguments): mixed;
}