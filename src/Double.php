<?php

namespace PhpSpec\Mock;

use PhpSpec\Mock\CodeGeneration\DoubleGenerator;
use PhpSpec\Mock\Double\CollaboratorClassDoesNotExistException;
use PhpSpec\Mock\Double\DoubleConfiguration;
use PhpSpec\Mock\Matcher\Registry\MatcherRegistry;
use PhpSpec\Mock\Wrapper\Registry\WrapperRegistry;

final readonly class Double
{
    private function __construct() {}

    /**
     * @param string|null $name null means anonymous
     * @return DoubleConfiguration
     * @throws CollaboratorClassDoesNotExistException
     */
    public static function create(
        ?string $name = null,
        ?MatcherRegistry $matchers = null,
        ?WrapperRegistry $wrappers = null
    ): DoubleConfiguration
    {
        [$classCode, $className, $metadata] = new DoubleGenerator()->generate($name);

        eval($classCode);

        return new DoubleConfiguration(
            new $className(),
            $matchers,
            $wrappers,
            $metadata
        );
    }
}
