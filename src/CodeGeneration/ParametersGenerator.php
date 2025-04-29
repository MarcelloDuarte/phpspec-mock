<?php

namespace PhpSpec\Mock\CodeGeneration;

class ParametersGenerator
{
    use TypeReflection;

    public function generate(\ReflectionMethod $method): string
    {
        $params = [];

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->hasType() ? $this->getTypeDeclaration($parameter->getType()) . ' ' : '';
            $variadic = $parameter->isVariadic() ? '...' : '';
            $param = $type . $variadic . '$' . $parameter->name;

            if ($parameter->isOptional() && !$parameter->isVariadic()) {
                $param .= ' = null';
            }

            $params[] = $param;
        }

        return implode(', ', $params);
    }
}