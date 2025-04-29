<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\Wrapper\DoubledMethod;

interface DoubleInterface
{
    public function addDoubledMethod(DoubledMethod $doubledMethod): void;
}
