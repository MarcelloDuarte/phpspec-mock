<?php

namespace Phpspec\Mock\CodeGeneration;

class ClassGenerator
{
    public function generate(string $className, string $extendsOrImplements, string $methods): string
    {
        return <<<CODE
class $className $extendsOrImplements
{
    private \\Phpspec\\Mock\\Doubler \$doubler;

    public function __construct()
    {
        \$this->doubler = new \\Phpspec\\Mock\\Doubler();
    }

    $methods

    public function __setMode(\\Phpspec\\Mock\\DoubleMode \$mode): void
    {
        \$this->doubler->mode = \$mode;
    }

    public function __getMode(): \\Phpspec\\Mock\\DoubleMode
    {
        return \$this->doubler->mode;
    }

    public function getDoubler(): \\Phpspec\\Mock\\Doubler
    {
        return \$this->doubler;
    }
}
CODE;
    }
}