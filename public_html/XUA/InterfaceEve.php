<?php


namespace XUA;


use Throwable;

abstract class InterfaceEve extends XUA
{
    protected static array $bind = [];

    protected static function _init()
    {
        $bind['eve'] = [];
    }

    /**
     * @throws Throwable
     */
    public abstract static function execute() : string;
}