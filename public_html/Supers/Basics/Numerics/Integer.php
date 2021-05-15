<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property bool unsigned
 * @method static SuperArgumentSignature A_unsigned() The Signature of: Argument `unsigned`
 */
class Integer extends Number
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'unsigned' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_numeric($input)) {
            $message = 'Value of type ' . gettype($input) . ' is not a number.';
            return false;
        }

        if ($input != floor($input)) {
            $message = "Value $input is not an integer.";
            return false;
        }

        if ($this->unsigned and $input < 0) {
            $message = "$input is less than zero therefore is not unsigned.";
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