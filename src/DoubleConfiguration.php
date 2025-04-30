<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\DoubleInterface as DoubleObject;
use PhpSpec\Mock\Matcher\MatcherRegistry;
use PhpSpec\Mock\Matcher\ShouldBeCalledMatcher;
use PhpSpec\Mock\Matcher\ShouldNotBeCalledMatcher;
use PhpSpec\Mock\Wrapper\CompositeDoubledMethod;
use PhpSpec\Mock\Wrapper\DoubledMethod;
use PhpSpec\Mock\Wrapper\MockedMethod;
use PhpSpec\Mock\Wrapper\StubbedMethod;
use PhpSpec\Mock\Wrapper\WrapperRegistry;

final class DoubleConfiguration
{
    private array $wrappers = [];

    public function __construct(
        private readonly DoubleObject $double,
        private ?WrapperRegistry $wrapperRegistry = null,
        private ?MatcherRegistry $matcherRegistry = null,
    )
    {
        $this->wrapperRegistry ??= new WrapperRegistry();
        $this->matcherRegistry ??= new MatcherRegistry();
        $this->registerDefaultWrappers();
        $this->registerDefaultMatchers();
    }

    public function stub(): object
    {
        return $this->double;
    }

    public function mock(): object
    {
        return $this->double;
    }

    public function dummy(): object
    {
        return $this->double;
    }

    public function __call(string $methodName, array $arguments = []): DoubledMethod
    {
        $existing = $this->findConfiguredMethod($methodName, $arguments);
        if ($existing !== null) {
            return $existing;
        }

        return $this->configureNewMethod($methodName, $arguments);
    }

    public function verify(): void
    {
        foreach ($this->wrappers as $wrapper) {
            if ($wrapper instanceof MockedMethod) {
                $wrapper->verify();
            }
        }
    }

    private function registerDefaultWrappers(): void
    {
        $this->wrapperRegistry->addWrapper(function ($method, $args) {
            $mocked = new MockedMethod($method, $args);
            $stubbed = new StubbedMethod($method, $args);
            $mocked->registerMatchers($this->matcherRegistry);
            return new CompositeDoubledMethod($mocked, $stubbed);
        });
    }

    private function registerDefaultMatchers(): void
    {
        $this->matcherRegistry->addMatcher(new ShouldBeCalledMatcher());
        $this->matcherRegistry->addMatcher(new ShouldNotBeCalledMatcher());
    }

    private function findConfiguredMethod(string $methodName, array $arguments): ?DoubledMethod
    {
        foreach ($this->wrappers as $method) {
            if ($method->satisfies($methodName, $arguments)) {
                return $method;
            }
        }
        return null;
    }

    private function configureNewMethod(string $methodName, array $arguments): DoubledMethod
    {
        foreach ($this->wrapperRegistry->all() as $factory) {
            $method = $factory($methodName, $arguments);

            $this->wrappers[] = $method;
            $this->double->addDoubledMethod($method);

            return $method;
        }

        throw new \RuntimeException("Method $methodName does not exist");
    }
}
