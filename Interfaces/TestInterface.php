<?php

namespace Interfaces;

use Services\EnvironmentService;
use XUA\InterfaceEve;

class TestInterface extends InterfaceEve
{
    public static function execute(): string
    {
        return 'Hello World! This is the ' . EnvironmentService::getEnv() . ' environment.';
    }
}