<?php

namespace Xua\Core\Interfaces;

use Xua\Core\Services\URPIService;
use Xua\Core\Eves\InterfaceEve;

class UniversalResourcePoolInterface extends InterfaceEve
{
    final public static function execute(): void
    {
        URPIService::$service::setOriginHeaders();
        URPIService::$service::main();
    }
}