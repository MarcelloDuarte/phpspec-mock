<?php

namespace Phpspec\Mock;

use Phpspec\Mock\DoubleInterface as DoubleObject;

readonly final class DoubleConfiguration
{
    public function __construct(private DoubleObject $double)
    {
    }

    public function stub(): object
    {
        return $this->double;
    }

    public function mock(): object
    {
        return $this->double;
    }

    public function dummy(): object
    {
        return $this->double;
    }

    public function __call(string $method, array $args): DoubledMethod
    {
        $doubledMethod = new DoubledMethod($method, $args);
        $this->double->getDoubler()->addDoubledMethod($doubledMethod);
        return $doubledMethod;
    }
}
