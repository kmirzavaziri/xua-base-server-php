<?php


namespace Services\XUA;


use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class SecurityService extends Service
{
    private static bool $hasPrivateMethodAccess = false;
    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `FlagService`.');
    }

    protected static function _init(): void
    {
        self::$hasPrivateMethodAccess = (isset($_SERVER['HTTP_XUA_INTERNAL_URPI_KEY']) and $_SERVER['HTTP_XUA_INTERNAL_URPI_KEY'] == ConstantService::get('config/XUA/sec', 'urpikey'));
    }

    public static function verifyPrivateMethodAccess() : bool
    {
        return self::$hasPrivateMethodAccess;
    }
}