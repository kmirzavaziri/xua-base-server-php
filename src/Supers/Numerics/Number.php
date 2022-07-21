<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Supers\Boolean;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 */
class Number extends Super
{
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::nullable, false, false,
                    new Boolean([])
                ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_numeric($input)) {
            $message = 'Value of type ' . gettype($input) . ' is not a number or value castable to a number.'; // @TODO message from dict
            return false;
        }

        return true;
    }

    protected function _unmarshal($input): mixed
    {
        if (is_numeric($input)) {
            return $input + 0;
        } else {
            return $input;
        }
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? 'null|' : '') . 'int|float';
    }
}