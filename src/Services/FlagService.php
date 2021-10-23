<?php


namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class FlagService extends Service
{
    private static array $flags = [];

    private function __construct() {}

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