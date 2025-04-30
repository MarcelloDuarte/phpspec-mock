<?php

namespace PhpSpec\Mock\CodeGeneration;

class MethodMetadata
{
    public function __construct(
        private readonly string $name,
        private readonly string $returnType,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    public function noReturnAllowed(): bool
    {
        return in_array($this->returnType, ['void', 'never']);
    }

    public function isValidReturn($value): bool
    {
        $expected = $this->returnType;

        // No type declared? Accept anything
        if ($expected === null || $expected === 'mixed') {
            return true;
        }

        // Void or never? Should never be called — checked elsewhere
        if (in_array($expected, ['void', 'never'], true)) {
            return false;
        }

        // Split union types
        $allowedTypes = explode('|', $expected);

        $actualType = get_debug_type($value);

        foreach ($allowedTypes as $type) {
            $type = ltrim($type, '?');

            // Handle PHP-style aliases
            if ($type === 'int' && is_int($value)) return true;
            if ($type === 'float' && is_float($value)) return true;
            if ($type === 'string' && is_string($value)) return true;
            if ($type === 'bool' && is_bool($value)) return true;
            if ($type === 'array' && is_array($value)) return true;
            if ($type === 'null' && $value === null) return true;
            if ($type === 'object' && is_object($value)) return true;
            if ($type === 'callable' && is_callable($value)) return true;
            if ($type === 'resource' && is_resource($value)) return true;

            // Class/interface name
            if (class_exists($type) || interface_exists($type)) {
                if ($value instanceof $type) return true;
            }
        }

        return false;
    }
}