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

    public function generate(\ReflectionMethod $method, string $body): array
    {
        $methodName = $method->getName();
        $parameters = $this->parametersGenerator->generate($method);
        $metadata = new MethodMetadata(
            $methodName, $method->hasReturnType() ?
                $method->getReturnType()->getName() :
                ''
        );
        $returnType = $method->hasReturnType() ? ': ' . $this->getTypeDeclaration($method->getReturnType()) : '';

        return [$metadata, <<<CODE
    #[\\ReturnTypeWillChange]
    public function $methodName($parameters)$returnType
    {
        $body
    }

CODE];
    }
}