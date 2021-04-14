<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

class Integer extends Number
{
    protected static function _arguments(): array
    {
        return [
            'unsigned' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ] + parent::_arguments();
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
        return 'INT';
    }
}