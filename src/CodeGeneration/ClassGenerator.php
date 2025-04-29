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

    public function __setMode(\\PhpSpec\\Mock\\DoubleMode \$mode): void
    {
        \$this->doubler->mode = \$mode;
    }

    public function __getMode(): \\PhpSpec\\Mock\\DoubleMode
    {
        return \$this->doubler->mode;
    }

    public function getDoubler(): \\PhpSpec\\Mock\\Doubler
    {
        return \$this->doubler;
    }
}
CODE;
    }
}