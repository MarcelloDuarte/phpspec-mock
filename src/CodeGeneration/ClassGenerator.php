<?php

namespace PhpSpec\Mock\CodeGeneration;

class ClassGenerator
{
    public function generate(
        string $className,
        string $extendsOrImplements,
        string $methods,
        bool $isReadOnly = false
    ): string
    {
        $readOnly = '';
        if ($isReadOnly) {
            $readOnly = 'readonly ';
        }
        return $readOnly . <<<CODE
class $className $extendsOrImplements
{
    private \\PhpSpec\\Mock\\Doubler \$doubler;

    public function __construct()
    {
        \$this->doubler = new \\PhpSpec\\Mock\\Doubler();
    }

$methods

    public function addDoubledMethod(
        \\PhpSpec\\Mock\\Wrapper\\DoubledMethod \$doubledMethod,
        \\PhpSpec\\Mock\\CodeGeneration\\MethodMetadata \$metadata
    ): void
    {
        \$this->doubler->addDoubledMethod(\$doubledMethod, \$metadata);
    }
}
CODE;
    }
}