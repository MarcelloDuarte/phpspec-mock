<?php

namespace Phpspec\Mock;

class Doubler
{
    /**
     * @var DoubledMethod[]
     */
    private iterable $doubledMethods = [];

    public function addDoubledMethod(DoubledMethod $doubledMethod): void
    {
        $this->doubledMethods[] = $doubledMethod;
    }

    public function call($name, $arguments)
    {
        foreach ($this->doubledMethods as $doubledMethod) {

            if ($doubledMethod->satisfies($name, $arguments)) {

                if ($doubledMethod->isThrowing()) {
                    $doubledMethod->throwException();
                }

                if ($doubledMethod->hasStubs()) {
                    return $doubledMethod->stubbedValue();
                }
            }
        }
        throw new \BadMethodCallException("Method $name was not stubbed");
    }
}
