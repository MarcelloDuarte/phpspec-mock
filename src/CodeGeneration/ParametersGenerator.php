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
                $default = var_export($parameter->getDefaultValue(), true);
                $param .= ' = ' . $default;
            }

            $params[] = $param;
        }

        return implode(', ', $params);
    }
}