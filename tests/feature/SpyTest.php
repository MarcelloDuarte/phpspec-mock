<?php

namespace Tests\Feature\PhpSpec\Mock;

use PhpSpec\Mock\Double;
use PhpSpec\Mock\Wildcard\Argument;
use PHPUnit\Framework\TestCase;

class SpyTest extends TestCase
{
    public function testCanSpyOnMethodCall()
    {
        $double = Double::create(SomeService::class);
        $service = $double->spy();

        // Call the method (exercise phase)
        $service->doSomething('ping');

        // Now assert (verify phase)
        $double->doSomething(Argument::exact('ping'))->shouldHaveBeenCalled();

        $this->expectNotToPerformAssertions(); // Test passes if no exception is thrown
    }
}

class SomeService
{
    public function doSomething(string $message): void
    {
        // maybe log or something
    }
}