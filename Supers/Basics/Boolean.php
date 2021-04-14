<?php


namespace Supers\Basics;


use XUA\Super;

class Boolean extends Super
{
    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Value of type ' . gettype($input) . ' is not bool.';
        return is_bool($input);
    }

    protected function _databaseType(): ?string
    {
        return 'BOOL NOT NULL';
    }
}