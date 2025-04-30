<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\Matcher\AnyArgumentsMatcher;
use PhpSpec\Mock\Matcher\AnyMatcher;
use PhpSpec\Mock\Matcher\ExactMatcher;

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
