<?php

namespace Tests\PhpSpec\Mock\Wrapper;

use PhpSpec\Mock\Wrapper\StubbedMethod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StubbedMethodTest extends TestCase
{
    public function testWillReturnStoredReturnValues()
    {
        $method = new StubbedMethod('foo', [1, 2]);
        $method->willReturn('bar');

        $this->assertTrue($method->hasStubs());
        $this->assertSame('bar', $method->stubbedValue());
    }

    public function testWillThrowStoredExceptionObject()
    {
        $method = new StubbedMethod('foo');
        $exception = new RuntimeException('Oops');
        $method->willThrow($exception);

        $this->assertTrue($method->isThrowing());
    }

    public function testWillThrowStoredExceptionClassName()
    {
        $method = new StubbedMethod('foo');
        $method->willThrow(RuntimeException::class);

        $this->assertTrue($method->isThrowing());
    }

    public function testSatisfiesReturnsTrueWhenMatching()
    {
        $method = new StubbedMethod('foo', [1, 2]);
        $this->assertTrue($method->satisfies('foo', [1, 2]));
    }

    public function testSatisfiesReturnsFalseWhenNotMatching()
    {
        $method = new StubbedMethod('foo', [1, 2]);
        $this->assertFalse($method->satisfies('bar', [1, 2]));
        $this->assertFalse($method->satisfies('foo', [1])); // wrong arguments
    }

    public function testThrowExceptionThrowsStoredObject()
    {
        $this->expectException(RuntimeException::class);

        $method = new StubbedMethod('foo');
        $method->willThrow(new RuntimeException('Test'));
        $method->throwException();
    }

    public function testThrowExceptionThrowsStoredClassName()
    {
        $this->expectException(RuntimeException::class);

        $method = new StubbedMethod('foo');
        $method->willThrow(RuntimeException::class);
        $method->throwException();
    }
}