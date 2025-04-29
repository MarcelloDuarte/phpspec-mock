<?php

namespace PhpSpec\Mock\Matcher;

interface CallRecorder
{
    public function getMethodName(): string;
}