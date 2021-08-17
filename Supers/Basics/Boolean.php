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
        return 'TINYINT(1) NOT NULL';
    }

    protected function _phpType(): string
    {
        return 'bool';
    }

    protected function _unmarshal(mixed $input): mixed
    {
        return (bool)$input;
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        return self::_unmarshal($input);
    }

}