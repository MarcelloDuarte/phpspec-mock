<?php

namespace PhpSpec\Mock\CodeGeneration;

class ClassGenerator
{
    public function generate(string $className, string $extendsOrImplements, string $methods): string
    {
        return <<<CODE
class $className $extendsOrImplements
{
    private \\PhpSpec\\Mock\\Doubler \$doubler;

    public function __construct()
    {
        \$this->doubler = new \\PhpSpec\\Mock\\Doubler();
    }

$methods

    public function addDoubledMethod(\\PhpSpec\\Mock\\Wrapper\\DoubledMethod \$doubledMethod): void
    {
        \$this->doubler->addDoubledMethod(\$doubledMethod);
    }
}
CODE;
    }
}