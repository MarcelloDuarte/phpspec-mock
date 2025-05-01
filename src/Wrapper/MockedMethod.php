<?php

namespace PhpSpec\Mock\Wrapper;


use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;
use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\ExpectationMatcherInterface;
use PhpSpec\Mock\Matcher\MatcherRegistry;

final class MockedMethod implements DoubledMethod, ObjectWrapper, CallRecorder, Matchable
{
    private array $calls = [];
    private array $matchers = [];
    private array $expectations = [];
    private ?MethodMetadata $metadata = null;

    public function __construct(private string $name, private readonly array $arguments = [])
    {}

    public function __call($method, $arguments)
    {
        foreach ($this->matchers[self::class] as $matcher) {
            if ($matcher instanceof ExpectationMatcherInterface && $matcher->supports($method)) {
                $this->expectations[] = $matcher->expect($this, $arguments);
                return $this;
            }
        }

        throw new \BadMethodCallException("Unknown matcher method: $method");
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

    public function recordCall(array $arguments = []): void
    {
        $this->calls[] = $arguments;
    }

    public function verify(): void
    {
        foreach ($this->expectations as $expectation) {
            foreach ($this->matchers[self::class] as $matcher) {
                $matcher->checkExpectation($expectation);
            }
        }
    }

    public function call(string $name, array $arguments)
    {
        $this->recordCall($arguments);
    }

    public function registerMatchers(MatcherRegistry $registry): void
    {
        $this->matchers = $registry->all();
    }

    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getMethodName(): string
    {
        return $this->name;
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
