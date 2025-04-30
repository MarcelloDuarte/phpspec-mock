<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Wrapper\DoubledMethod;

interface DoubleInterface
{
    public function addDoubledMethod(DoubledMethod $doubledMethod, MethodMetadata $metadata): void;
}
