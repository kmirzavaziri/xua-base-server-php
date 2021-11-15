<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Boolean;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property bool unsigned
 */
class Integer extends Number
{
    const nullable = self::class . '::nullable';
    const unsigned = self::class . '::unsigned';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::unsigned, false, false,
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
            $message = 'Value of type ' . gettype($input) . ' is not a number.';
            return false;
        }

        if ($input != floor($input)) {
            $message = ExpressionService::get('xua.supers.numerics.integer.error_message.value_is_not_an_integer', [
                'value' => $input
            ]);
            return false;
        }

        if ($this->unsigned and $input < 0) {
            $message = ExpressionService::get('xua.supers.numerics.integer.error_message.value_is_less_than_zero_therefore_is_not_unsigned', [
                'value' => $input
            ]);
            return false;
        }

        return true;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "INT$nullExpression";
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'int';
    }
}