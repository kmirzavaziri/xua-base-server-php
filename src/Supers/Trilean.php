<?php

namespace Xua\Core\Supers;

use Xua\Core\Eves\Super;

class Trilean extends Super
{
    protected function _predicate($input, null|string|array &$message = null): bool
    {
        $message = 'Value of type ' . gettype($input) . ' is not bool and is not null.';
        return (is_bool($input) or $input === null);
    }

    protected function _databaseType(): ?string
    {
        return "BOOL NULL";
    }

    protected function _phpType(): string
    {
        return '?bool';
    }
}