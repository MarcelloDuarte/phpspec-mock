<?php

namespace PhpSpec\Mock\CodeGeneration;

class MethodGenerator
{
    use TypeReflection;

    private ParametersGenerator $parametersGenerator;

    public function __construct(ParametersGenerator $parametersGenerator)
    {
        $this->parametersGenerator = $parametersGenerator;
    }

    public function generate(\ReflectionMethod $method, string $body): string
    {
        $methodName = $method->getName();
        $parameters = $this->parametersGenerator->generate($method);
        $returnType = $method->hasReturnType() ? ': ' . $this->getTypeDeclaration($method->getReturnType()) : '';

        return <<<CODE
    #[\\ReturnTypeWillChange]
    public function $methodName($parameters)$returnType
    {
        $body
    }

CODE;
    }
}