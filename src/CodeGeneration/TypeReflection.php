<?php

namespace PhpSpec\Mock\CodeGeneration;

trait TypeReflection
{
    public function getTypeDeclaration(\ReflectionType $type): string
    {
        if ($type instanceof \ReflectionNamedType) {
            return $this->formatType($type);
        }

        if ($type instanceof \ReflectionUnionType) {
            return implode('|', array_map(function ($innerType) {
                return $this->formatType($innerType);
            }, $type->getTypes()));
        }

        if ($type instanceof \ReflectionIntersectionType) {
            return implode('&', array_map(fn($t) => $this->formatType($t), $type->getTypes()));
        }

        return (string) $type;
    }

    private function formatType(\ReflectionNamedType $type): string
    {
        $name = $type->getName();

        if (in_array($name, ['mixed', 'void', 'never'], true)) {
            return $name;
        }

        return ($type->allowsNull() ? '?' : '') . $name;
    }
}
