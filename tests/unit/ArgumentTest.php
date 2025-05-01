<?php

namespace Tests\PhpSpec\Mock;

use PhpSpec\Mock\Argument;
use PhpSpec\Mock\Double;
use PhpSpec\Mock\Matcher\ArgumentMatcherInterface;
use PHPUnit\Framework\TestCase;

class ArgumentTest extends TestCase
{
    public function testAnyMatcherAlwaysMatches()
    {
        $matcher = Argument::any();
        $this->assertInstanceOf(ArgumentMatcherInterface::class, $matcher);

        $this->assertTrue($matcher->matches(null));
        $this->assertTrue($matcher->matches('foo'));
        $this->assertTrue($matcher->matches(123));
        $this->assertTrue($matcher->matches(['anything']));
        $this->assertTrue($matcher->matches(new \stdClass()));
    }

    public function testCeteraMatcherMatchesAllValues()
    {
        $matcher = Argument::cetera();
        $this->assertInstanceOf(ArgumentMatcherInterface::class, $matcher);

        $this->assertTrue($matcher->matches(null));
        $this->assertTrue($matcher->matches('foo'));
        $this->assertTrue($matcher->matches(123));
        $this->assertTrue($matcher->matches([1, 2, 3]));
        $this->assertTrue($matcher->matches(new \stdClass()));
    }

    public function testExactMatcherMatchesAValueExactly()
    {
        $matcher = Argument::exact('foo');
        $this->assertInstanceOf(ArgumentMatcherInterface::class, $matcher);

        $this->assertTrue($matcher->matches('foo'));
        $this->assertFalse($matcher->matches('bar'));
        $this->assertFalse($matcher->matches(null));
    }

    public function testExactMatcherWithNumbers()
    {
        $matcher = Argument::exact(42);

        $this->assertTrue($matcher->matches(42));
        $this->assertFalse($matcher->matches('42')); // strict match
        $this->assertFalse($matcher->matches(0));
    }

    public function testAnyArgumentMatcherMatchesAnyValue()
    {
        $mock = Double::create(SomeOtherService::class);

        $mock->someMethod(Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn('foo');

        $instance = $mock->stub();
        $instance->someMethod('whatever', 42);

        $mock->verify(); // should not throw
        $this->expectNotToPerformAssertions();
    }

    public function testExactArgumentMatcherOnlyMatchesExactValue()
    {
        $mock = Double::create(SomeOtherService::class);

        $mock->someMethod(Argument::exact('foo'), Argument::exact(42))
            ->willReturn('bar')
            ->shouldBeCalled();

        $instance = $mock->stub();
        $instance->someMethod('foo', 42);

        $mock->verify(); // should not throw
        $this->expectNotToPerformAssertions();
    }

    public function testCeteraMatcherMatchesAnyArguments()
    {
        $mock = Double::create(SomeOtherService::class);
        $mock->someMethod(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn('bar');

        $instance = $mock->stub();
        $instance->someMethod('foo', 123, ['deep'], new \stdClass());

        $mock->verify(); // should not throw
        $this->expectNotToPerformAssertions();
    }

    public function testChainedShouldBeCalledAndWillReturnWorkTogether()
    {
        $mock = Double::create(SomeOtherService::class);

        $mock->someMethod(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn('hello world');

        $instance = $mock->stub();
        $result = $instance->someMethod('anything', 42);

        $this->assertSame('hello world', $result);

        $mock->verify(); // should not throw
    }
}

class SomeOtherService
{
    public function someMethod(string $arg, int $other): string {
        return $arg;
    }
}