<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Wrapper\DoubledMethod;

class Doubler
{
    private iterable $doubledMethods = [];

    public function addDoubledMethod(DoubledMethod $doubledMethod, MethodMetadata $metadata): void
    {
        $doubledMethod->setMetadata($metadata);
        $this->doubledMethods[] = $doubledMethod;
    }

    public function call(string $name, array $arguments): mixed
    {
        foreach ($this->doubledMethods as $doubledMethod) {

            if ($doubledMethod->satisfies($name, $arguments)) {
                return $doubledMethod->call($name, $arguments);
            }
        }

        throw new \BadMethodCallException("Method $name was not stubbed");
    }
}
