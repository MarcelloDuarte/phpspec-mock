<?php

namespace PhpSpec\Mock;

class Doubler
{
    /**
     * @var StubbedMethod[]
     */
    private iterable $doubledMethods = [];

    public function addDoubledMethod(DoubledMethod $doubledMethod): void
    {
        $this->doubledMethods[] = $doubledMethod;
    }

    public function call($name, $arguments): mixed
    {
        foreach ($this->doubledMethods as $doubledMethod) {

            if ($doubledMethod->satisfies($name, $arguments)) {
                return $doubledMethod->call($name, $arguments);
            }
        }

        throw new \BadMethodCallException("Method $name was not stubbed");
    }
}
