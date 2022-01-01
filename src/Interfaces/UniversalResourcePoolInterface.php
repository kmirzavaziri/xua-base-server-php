<?php

namespace Xua\Core\Interfaces;

use Xua\Core\Services\URPIService;
use Xua\Core\Eves\InterfaceEve;

class UniversalResourcePoolInterface extends InterfaceEve
{
    final public static function execute(): void
    {
        /** @var URPIService $service */
        $service = static::service();
        $service::setOriginHeaders();
        $service::main();
    }

    protected static function service(): string
    {
        return URPIService::class;
    }

}