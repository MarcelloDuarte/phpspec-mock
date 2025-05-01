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
     *  Creates a fully configurable double for the given class or interface.
     *
     *  This double supports stubbing, mocking, and spying depending on how it is configured.
     *  If no class name is provided, an anonymous double implementing the `DoubleInterface` will be created.
     *
     *  Optionally accepts custom matcher and wrapper registries to extend or override default behavior.
     *
     * @param string|null $name null means anonymous
     * @param MatcherRegistry|null $matchers
     * @param WrapperRegistry|null $wrappers
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

    /**
     * Create a quick dummy double for the given class or interface.
     * This method bypasses expectations/stubbing configuration.
     *
     * @param string $name Fully-qualified class or interface name to double
     * @return DoubleInterface
     */
    public static function dummy(string $name): DoubleInterface
    {
        [$code, $dummyName] = new DoubleGenerator()->generate($name);
        eval($code);
        return new $dummyName();
    }
}
