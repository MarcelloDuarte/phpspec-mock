<?php

namespace Phpspec\Mock\CodeGeneration;

class ParametersGenerator
{
    use TypeReflection;

    public function generate(array $reflectionParameters): array
    {
        $parameters = [];
        $variables = [];

        foreach ($reflectionParameters as $parameter) {
            $type = $parameter->hasType() ? $this->getTypeDeclaration($parameter->getType()) . ' ' : '';
            $variadic = $parameter->isVariadic() ? '...' : '';
            $param = $type . $variadic . '$' . $parameter->name;

            if ($parameter->isDefaultValueAvailable() && !$parameter->isVariadic()) {
                $param .= ' = ' . var_export($parameter->getDefaultValue(), true);
            }

            $parameters[] = $param;
            $variables[] = ($parameter->isVariadic() ? '...' : '') . '$' . $parameter->name;
        }

        return [implode(', ', $parameters), $variables];
    }
}