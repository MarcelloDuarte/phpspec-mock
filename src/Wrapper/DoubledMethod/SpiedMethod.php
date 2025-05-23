<?php

namespace PhpSpec\Mock\Wrapper\DoubledMethod;

use PhpSpec\Mock\CodeGeneration\MethodMetadata;
use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;
use PhpSpec\Mock\Matcher\CallRecorder;
use PhpSpec\Mock\Matcher\ExpectationMatcherInterface;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Matcher\Runner\MatcherRunner;
use PhpSpec\Mock\Wrapper\DoubledMethod;
use PhpSpec\Mock\Wrapper\Matchable;
use PhpSpec\Mock\Wrapper\ObjectWrapper;
use PhpSpec\Mock\Wrapper\Silent;

final class SpiedMethod implements DoubledMethod, ObjectWrapper, CallRecorder, Matchable
{
    private array $calls = [];
    private array $matchers = [];
    private array $expectations = [];
    private ?MethodMetadata $metadata = null;

    public function __construct(
        private string $name,
        private readonly array $arguments = [],
        private ?MatcherRunner $matcherRunner = null
    ) {
        $this->matcherRunner ??= new MatcherRunner();
    }

    public function __call($method, $arguments)
    {
        $matchers = $this->matchers[self::class] ?? [];

        $expectation = $this->matcherRunner->run($matchers, $this, $method, $arguments);

        if ($expectation !== null) {
            $this->expectations[] = $expectation;
            return $this;
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

    public function call(string $name, array $arguments): void
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

    public function setMetadata(MethodMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getExpectedArgs(): array
    {
        return $this->arguments;
    }
}