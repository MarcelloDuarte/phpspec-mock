<?php

namespace PhpSpec\Mock\Double;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;
use PhpSpec\Mock\Matcher\Expectation\ExpectationException;
use PhpSpec\Mock\Wrapper\DoubledMethod;

class Doubler
{
    private iterable $doubledMethods = [];

    public function addDoubledMethod(DoubledMethod $doubledMethod, MethodMetadata $metadata): void
    {
        $doubledMethod->setMetadata($metadata);
        $this->doubledMethods[] = $doubledMethod;
    }

    public function call(string $name, array $arguments): mixed
    {
        foreach ($this->doubledMethods as $doubledMethod) {
            if ($doubledMethod->satisfies($name, $arguments)) {
                return $doubledMethod->call($name, $arguments);
            }
        }

        $calledArgs = json_encode($arguments);

        $knownStubs = array_map(
            fn($method) => sprintf(
                '- %s(%s)',
                $method->getMethodName(),
                $this->formatArgs($method->getExpectedArgs())
            ),
            iterator_to_array($this->doubledMethods)
        );

        throw new ExpectationException(sprintf(
            "No stubbed value found for method \"%s()\" with arguments:\n" .
            "  Called with: %s\n" .
            "  Known stubs:\n    %s",
            $name,
            $calledArgs,
            implode("\n    ", $knownStubs)
        ));
    }

    private function formatArgs(array $args): string
    {
        return '[' . implode(', ', array_map(function ($arg) {
                if ($arg instanceof ArgumentMatcherInterface) {
                    return (string) $arg;
                }

                return json_encode($arg);
            }, $args)) . ']';
    }
}
