<?php

namespace Xua\Core\Eves;

abstract class SingletonService extends Service
{
    private static array $_x_instances = [];

    protected function __construct() {}

    /**
     * When extending SingletonService, define a public static function getInstance, specify args there, and return the
     * result of this function.
     * @param ...$args
     * @return static
     */
    protected static function _x_getInstance(...$args): static
    {
        $className = static::getInstanceClassName(...$args);
        if (!isset(self::$_x_instances[$className])) {
            self::$_x_instances[$className] = new $className();
        }
        return self::$_x_instances[$className];
    }

    protected static function getInstanceClassName(...$args): string
    {
        return static::class;
    }
}