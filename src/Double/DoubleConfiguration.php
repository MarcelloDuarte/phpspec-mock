<?php

namespace PhpSpec\Mock\Double;

use PhpSpec\Mock\DoubleInterface as DoubleObject;
use PhpSpec\Mock\Matcher\Method\BeCalledMatcher;
use PhpSpec\Mock\Matcher\Method\HaveBeenCalledMatcher;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Matcher\Runner\MatcherRunner;
use PhpSpec\Mock\Wrapper\DoubledMethod;
use PhpSpec\Mock\Wrapper\DoubledMethod\CompositeDoubledMethod;
use PhpSpec\Mock\Wrapper\DoubledMethod\MockedMethod;
use PhpSpec\Mock\Wrapper\DoubledMethod\SpiedMethod;
use PhpSpec\Mock\Wrapper\DoubledMethod\StubbedMethod;
use PhpSpec\Mock\Wrapper\Registry\WrapperRegistry;

final class DoubleConfiguration
{
    private array $wrappers = [];

    public function __construct(
        private readonly DoubleObject $double,
        private ?WrapperRegistry $wrapperRegistry = null,
        private ?MatcherRegistry $matcherRegistry = null,
        private readonly array $metadata = [],
    )
    {
        $this->wrapperRegistry ??= new WrapperRegistry();
        $this->matcherRegistry ??= new MatcherRegistry();
        $this->registerDefaultWrappers();
        $this->registerDefaultMatchers();
    }

    public function double(): object
    {
        return $this->double;
    }

    public function stub(): object
    {
        return $this->double;
    }

    public function mock(): object
    {
        return $this->double;
    }

    public function spy(): object
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
            static $runner = new MatcherRunner();
            $mocked = new MockedMethod($method, $args, $runner);
            $spied = new SpiedMethod($method, $args, $runner);
            $stubbed = new StubbedMethod($method, $args);
            $mocked->registerMatchers($this->matcherRegistry);
            return new CompositeDoubledMethod($mocked, $spied, $stubbed);
        });
    }

    private function registerDefaultMatchers(): void
    {
        $this->matcherRegistry->addMatcher(MockedMethod::class, new BeCalledMatcher());
        $this->matcherRegistry->addMatcher(SpiedMethod::class, new HaveBeenCalledMatcher());
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
            $this->double->addDoubledMethod($method, $this->metadata[$methodName]);

            return $method;
        }

        throw new \RuntimeException("Method $methodName does not exist");
    }
}
