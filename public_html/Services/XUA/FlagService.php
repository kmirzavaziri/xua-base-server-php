<?php


namespace Services\XUA;


use Exception;
use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class FlagService extends Service
{
    private static array $flags = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `FlagService`.');
    }

    public static function get(string $key) : mixed
    {
        return self::$flags[$key] ?? null;
    }

    public static function set(string $key, mixed $value) : void
    {
        self::$flags[$key] = $value;
    }

    public static function unset(string $key) : void
    {
        unset(self::$flags[$key]);
    }
}