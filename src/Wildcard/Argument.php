<?php

namespace PhpSpec\Mock\Wildcard;

use PhpSpec\Mock\Matcher\Argument\AnyArgumentsMatcher;
use PhpSpec\Mock\Matcher\Argument\AnyMatcher;
use PhpSpec\Mock\Matcher\Argument\ExactMatcher;

final class Argument
{
    public static function any(): AnyMatcher
    {
        return new AnyMatcher();
    }

    public static function exact(mixed $value): ExactMatcher
    {
        return new ExactMatcher($value);
    }

    public static function cetera(): AnyArgumentsMatcher
    {
        return new AnyArgumentsMatcher();
    }
}
