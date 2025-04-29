<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\DoubleInterface as DoubleObject;

final class DoubleConfiguration
{
    private array $stubbedMethods = [];
    private array $mockedMethods = [];

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

    public function __call(string $methodName, array $arguments = []): StubbedMethod
    {
        $stubbedMethod = new StubbedMethod($methodName, $arguments);
        $mockedMethod = new MockedMethod($methodName, $arguments);

        $this->stubbedMethods[] = $stubbedMethod;
        $this->mockedMethods[] = $mockedMethod;

        $this->double->getDoubler()->addDoubledMethod($stubbedMethod);
        $this->double->getDoubler()->addDoubledMethod($mockedMethod);

        return $stubbedMethod;
    }

    public function verify(): void
    {
        foreach ($this->mockedMethods as $mockedMethod) {
            $mockedMethod->verify();
        }
    }
}
