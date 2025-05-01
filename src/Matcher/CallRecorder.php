<?php

namespace PhpSpec\Mock\Matcher;

interface CallRecorder
{
    public function recordCall(array $arguments = []): void;
    public function getMethodName(): string;
    public function getCalls(): array;
}