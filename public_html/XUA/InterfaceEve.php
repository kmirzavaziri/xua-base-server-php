<?php


namespace XUA;


use Services\XUA\TemplateService;

abstract class InterfaceEve
{
    public abstract static function execute() : string;
}