<?php


namespace XUA\Supers;


use XUA\Eves\Super;

class Universal extends Super
{
    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return true;
    }
}