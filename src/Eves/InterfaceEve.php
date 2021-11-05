<?php

namespace Xua\Core\Eves;

abstract class InterfaceEve extends Xua
{
    public abstract static function execute() : void;
}