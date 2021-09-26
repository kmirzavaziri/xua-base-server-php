<?php

namespace Interfaces;

use Entities\Item;
use Entities\Product;
use Services\EnvironmentService;
use Services\ItemService;
use XUA\InterfaceEve;

class TestInterface extends InterfaceEve
{
    public static function execute(): string
    {
        $item = new Item(3);
        $code = ItemService::generateCode($item);
        var_dump($code);
        return 'Hello World! This is the ' . EnvironmentService::getEnv() . ' environment.';
    }
}