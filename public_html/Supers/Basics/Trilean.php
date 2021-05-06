<?php


namespace Supers\Basics;


use XUA\Super;

class Trilean extends Super
{
    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Value of type ' . gettype($input) . ' is not bool and is not null.';
        return (is_bool($input) or $input === null);
    }

    protected function _databaseType(): ?string
    {
        return "BOOL";
    }

    protected function _phpType(): string
    {
        return '?bool';
    }
}