<?php


namespace XUA;


use Services\XUA\ConstantService;

abstract class InterfaceEve extends XUA
{
    protected static array $bind = [];

    protected static function _init()
    {
        $bind['eve'] = [];
    }

    public abstract static function execute() : string;
}