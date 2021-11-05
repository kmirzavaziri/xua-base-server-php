<?php

namespace Xua\Core\Supers;

use Xua\Core\Eves\Super;

class Universal extends Super
{
    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return true;
    }
}