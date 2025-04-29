<?php

namespace PhpSpec\Mock\Wrapper;


use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\ExpectationMatcherInterface;
use PhpSpec\Mock\Matcher\MatcherRegistry;

final class MockedMethod implements DoubledMethod, ObjectWrapper, CallRecorder
{
    private array $calls = [];
    private array $matchers = [];
    private array $expectations = [];

    public function __construct(private string $name, private readonly array $arguments = [])
    {}

    public function __call($method, $arguments)
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher instanceof ExpectationMatcherInterface && $matcher->supports($method)) {
                $this->expectations[] = $matcher->expect($this, $arguments);
                return $this;
            }
        }

        throw new \BadMethodCallException("Unknown matcher method: $method");
    }

    public function recordCall(array $arguments = []): void
    {
        $this->calls[] = $arguments;
    }

    public function satisfies(string $methodName, array $arguments = []): bool
    {
        return $methodName === $this->name && $arguments == $this->arguments;
    }

    public function verify(): void
    {
        foreach ($this->expectations as $expectation) {
            foreach ($this->matchers as $matcher) {
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
}
