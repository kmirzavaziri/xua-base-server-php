<?php

namespace Xua\Core\Eves;

abstract class Xua
{
    const VERSION = '1.0-β';

    private static array $initialized = [];

    final public static function init(): void
    {
        if (!isset(self::$initialized[static::class])) {
            static::_init();
            self::$initialized[static::class] = true;
        }
    }

    protected static function _init(): void
    {
        # Empty by default
    }
}