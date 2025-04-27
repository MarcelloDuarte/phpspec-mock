<?php

namespace Phpspec\Mock;

use ReflectionClass;

class DoubleFactory
{
    public static array $count = [];

    public static function create(?string $name = null): \Phpspec\Mock\Double
    {
        $namespaceToUnderscore = str_replace('\\', '_', $name ?? '');
        $doubleName = 'PhpSpecMock__' . $namespaceToUnderscore . '_' . self::count($name ?? '');
        $isInterface = !($name === null) && self::isInterface($name);

        $methods = '';
        if ($name !== null) {
            $methods = self::generateMethods($name);
        }

        eval(self::generateDoubleClass(
            $doubleName,
            $name ?? '',
            $isInterface,
            is_null($name),
            $methods
        ));

        if (isset($double)) {
            return $double;
        }
        return new $doubleName();
    }

    private static function count(string $name)
    {
        if (!isset(self::$count[$name])) {
            self::$count[$name] = 1;
        }

        return self::$count[$name]++;
    }

    private static function generateDoubleClass(
        string $doubleName,
        string $name,
        ?bool $isInterface = false,
        ?bool $isAnonymous = false,
        string $methods = ''
    ): string
    {
        $firstLine = "class $doubleName extends $name implements \Phpspec\Mock\Double";

        if ($isAnonymous) {
            $firstLine = '$double = new class() implements \Phpspec\Mock\Double';
        } elseif ($isInterface) {
            $firstLine = "class $doubleName implements $name, \Phpspec\Mock\Double";
        }

        return <<<DOUBLE
$firstLine
{
    private \Phpspec\Mock\Doubler \$doubler;
    
    public function __construct()
    {
        \$this->doubler = new \Phpspec\Mock\Doubler();
    }

    $methods

    public function __setMode(\Phpspec\Mock\DoubleMode \$mode): void
    {
        \$this->doubler->mode = \$mode;
    }

    public function __getMode(): \Phpspec\Mock\DoubleMode
    {
        return \$this->doubler->mode;
    }
};
DOUBLE;
    }

    private static function isInterface(string $name): bool
    {
        try {
            $reflectionClass = new ReflectionClass($name);
        } catch (\ReflectionException $e) {
            return false;
        }

        return $reflectionClass->isInterface();
    }

    private static function generateMethods(string $name): string
    {
        $methods = '';

        try {
            $reflectionClass = new ReflectionClass($name);

            $reflectionMethods = $reflectionClass->getMethods();

            foreach ($reflectionMethods as $reflectionMethod) {

                if ($reflectionMethod->isPublic()) {
                    [$parameters, $variableNames] = self::getParameters($reflectionMethod);

                    if ($reflectionMethod->isAbstract()) {
                        $methods .= 'abstract ';
                    }

                    $methods .= 'public function ' .
                        $reflectionMethod->name . '('.
                        $parameters .')';

                    if ($reflectionMethod->hasReturnType()) {
                        $methods .= ': ' . $reflectionMethod->getReturnType();
                    }

                    $methods .= PHP_EOL;
                    $methods .= '    {' . PHP_EOL;
                    $methods .= sprintf(
                        "        return \$this->doubler->call(\"%s\", [%s]);%s",
                        $reflectionMethod->name,
                        implode(', ', $variableNames), PHP_EOL);
                    $methods .= '    }' . PHP_EOL;
                    $methods .= PHP_EOL;
                }
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('ReflectionException: ' . $e->getMessage());
        }
        return $methods;
    }

    private static function getParameters(\ReflectionMethod $reflectionMethod): array
    {
        $parameters = '';
        $variableNames = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->hasType()) {
                $type = $parameter->getType();
                $parameters .= $type->getName() .' $' . $parameter->name;
                $variableNames[] = ' $' . $parameter->name;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $default = $parameter->getDefaultValue();
                $parameters .= ' = $default';
            }
            $parameters .= ', ';
        }

        if (str_ends_with($parameters, ', ')) {
            $parameters = substr($parameters, 0, -2);
        }

        return [$parameters, $variableNames];
    }
}