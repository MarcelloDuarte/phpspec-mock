<?php

namespace PhpSpec\Mock;


final class MockedMethod implements DoubledMethod
{
    private array $calls = [];
    private bool $shouldBeCalled = false;
    private ?int $expectedCalls = null;

    public function __construct(private readonly string $name, private readonly array $arguments = [])
    {}

    public function shouldBeCalled(?int $times = null): void
    {
        $this->shouldBeCalled = true;
        $this->expectedCalls = $times;
    }

    public function shouldNotBeCalled(): void
    {
        $this->shouldBeCalled = false;
        $this->expectedCalls = 0;
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
        $actualCalls = count($this->calls);

        if ($this->expectedCalls !== null) {
            if ($actualCalls !== $this->expectedCalls) {
                throw new \RuntimeException(sprintf(
                    'Expected method "%s" to be called %d times, but was called %d times.',
                    $this->name,
                    $this->expectedCalls,
                    $actualCalls
                ));
            }
        } elseif ($this->shouldBeCalled && $actualCalls === 0) {
            throw new \RuntimeException(sprintf(
                'Expected method "%s" to be called at least once, but it was not called.',
                $this->name
            ));
        } elseif (!$this->shouldBeCalled && $actualCalls > 0) {
            throw new \RuntimeException(sprintf(
                'Expected method "%s" not to be called, but it was called %d times.',
                $this->name,
                $actualCalls
            ));
        }
    }

    public function call(string $name, array $arguments)
    {
        $this->recordCall($arguments);
    }
}
