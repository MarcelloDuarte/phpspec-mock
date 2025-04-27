<?php

namespace Phpspec\Mock;

trait DoubleWrapper
{
    private \Phpspec\Mock\Doubler $doubler;
    private \Phpspec\Mock\DoubleMode $mode = \Phpspec\Mock\DoubleMode::ConfigurationMode;



}