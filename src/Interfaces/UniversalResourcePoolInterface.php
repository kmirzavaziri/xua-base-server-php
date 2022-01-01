<?php

namespace Xua\Core\Interfaces;

use Xua\Core\Services\URPIService;
use Xua\Core\Eves\InterfaceEve;

class UniversalResourcePoolInterface extends InterfaceEve
{
    final public static function execute(): void
    {
        static::service()::setOriginHeaders();
        static::service()::main();
    }

    protected static function service(): URPIService
    {
        return URPIService::class;
    }

}