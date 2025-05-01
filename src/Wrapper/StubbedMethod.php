<?php

namespace PhpSpec\Mock\Wrapper;

use Exception;
use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\AnyArgumentsMatcher;
use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;
use PhpSpec\Mock\Matcher\MatcherRegistry;

final class StubbedMethod implements DoubledMethod, ObjectWrapper, Satisfiable
{
    private array $stubs = [];
    private const string WILL_RETURN = 'willReturn';
    private const string WILL_THROW = 'willThrow';
    private const array CONFIGURATION_METHODS = [
        StubbedMethod::WILL_RETURN,
        StubbedMethod::WILL_THROW
    ];

    private null|Exception|string $exceptionToThrow = null;
    private ?MethodMetadata $metadata = null;

    public function __construct(private string $name, private array $arguments = [])
    {
    }

    public function willReturn(...$returnValues): void
    {
        if ($this->metadata instanceof MethodMetadata) {
            if ($this->metadata->noReturnAllowed()) {
                throw new \RuntimeException(sprintf(
                    'Cannot stub return value for method "%s": it is declared as void or never.',
                    $this->name
                ));
            }

            foreach ($returnValues as $returnValue) {
                if (! $this->metadata->isValidReturn($returnValue)) {
                    throw new \RuntimeException(sprintf(
                        'Cannot stub return value for method "%s": expected %s.',
                        $this->name,
                        $this->metadata->getReturnType()
                    ));
                }
            }
        }
        $this->stubs = $returnValues;
    }

    public function willThrow(Exception|string $exception): void
    {
        $this->exceptionToThrow = $exception;
    }

    public function isConfigurationMethod(string $name): bool
    {
        return in_array($name, StubbedMethod::CONFIGURATION_METHODS);
    }

    public function satisfies(string $methodName, array $arguments = []): bool
    {
        if ($methodName !== $this->name) {
            return false;
        }

        $expected = $this->arguments;
        $actual = $arguments;

        foreach ($expected as $i => $matcher) {
            if (!array_key_exists($i, $actual)) {
                return false;
            }

            if ($matcher instanceof ArgumentMatcherInterface) {
                if ($matcher->isVariadic()) {
                    return true;
                }

                if (!$matcher->matches($actual[$i])) {
                    return false;
                }
            } elseif ($matcher !== $actual[$i]) {
                return false;
            }
        }

        return count($actual) <= count($expected);
    }

    public function hasStubs(): bool
    {
        return $this->stubs !== [];
    }

    public function stubbedValue(): mixed
    {
        return current($this->stubs);
    }

    public function isThrowing(): bool
    {
        return $this->exceptionToThrow !== null;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function throwException(): void
    {
        if ($this->exceptionToThrow instanceof Exception) {
            throw $this->exceptionToThrow;
        } else {
            throw new $this->exceptionToThrow;
        }
    }

    public function call(string $name, array $arguments)
    {
        if ($this->isThrowing()) {
            $this->throwException();
        }

        if ($this->hasStubs()) {
            return $this->stubbedValue();
        }

        return null;
    }

    public function registerMatchers(MatcherRegistry $registry): void
    {
        // No-op for stubs
    }

    public function setMetadata(MethodMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getExpectedArgs(): array
    {
        return $this->arguments;
    }
}
