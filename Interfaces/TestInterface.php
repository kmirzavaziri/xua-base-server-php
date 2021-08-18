<?php

namespace Interfaces;

use XUA\InterfaceEve;

class TestInterface extends InterfaceEve
{
    public static function execute(): string
    {
        return 'Hello World!';
    }
}