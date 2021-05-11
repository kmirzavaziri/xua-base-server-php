<?php


namespace XUA;


abstract class XUA
{
    const VERSION = '1.0-β';

    private static array $initialized = [];

    final public static function init() {
        if (!isset(self::$initialized[static::class])) {
            static::_init();
            self::$initialized[static::class] = true;
        }
    }

    protected static function _init() {
        # Empty by default
    }
}