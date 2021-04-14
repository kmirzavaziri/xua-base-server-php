<?php


namespace Supers\Basics;


use XUA\Super;

class Universal extends Super
{
    protected function _predicate($input, string &$message = null): bool
    {
        return true;
    }
}