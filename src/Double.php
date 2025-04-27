<?php

namespace Phpspec\Mock;

use Phpspec\Mock\CodeGeneration\DoubleGenerator;

final readonly class Double
{
    private function __construct() {}

    /**
     * @param string|null $name null means anonymous
     * @return DoubleConfiguration
     * @throws CollaboratorClassDoesNotExistException
     */
    public static function create(?string $name = null): DoubleConfiguration
    {
        [$classCode, $className] = new DoubleGenerator()->generate($name);

        eval($classCode);

        return new DoubleConfiguration(new $className());
    }
}
