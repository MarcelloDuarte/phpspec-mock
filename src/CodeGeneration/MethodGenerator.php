<?php

namespace Phpspec\Mock\CodeGeneration;

class MethodGenerator
{
    public function generate(string $methodName, string $parameters, ?string $returnType, string $body): string
    {
        $returnTypeDeclaration = $returnType ? ": $returnType" : '';

        return <<<CODE
#[\ReturnTypeWillChange]
public function $methodName($parameters)$returnTypeDeclaration
{
    $body
}

CODE;
    }
}