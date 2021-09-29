<?php


namespace XUA\Eves;

abstract class InterfaceEve extends XUA
{
    public abstract static function execute() : string;
}