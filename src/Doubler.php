<?php

namespace Phpspec\Mock;

class Doubler
{
    public DoubleMode $mode = DoubleMode::ConfigurationMode {
        get {
            return $this->mode;
        }
        set(DoubleMode $value) {
            $this->mode = $value;
        }
    }
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
        return match ($this->mode) {
            DoubleMode::ConfigurationMode => (function(){
                $doubledMethod = new DoubledMethod('someMethod', []);
                $this->addDoubledMethod($doubledMethod);

                return $doubledMethod;
            })(),
            DoubleMode::ExecutionMode => (function() use ($name, $arguments) {
                foreach ($this->doubledMethods as $doubledMethod) {
                    if ($doubledMethod->satisfies($name, $arguments)) {
                        return $doubledMethod->stubbedValue();
                    }
                }
                throw new \BadMethodCallException("Method $name was not stubbed");
            })()
        };
    }

}