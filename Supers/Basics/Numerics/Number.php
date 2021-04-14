<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

class Number extends Super
{
    protected static function _arguments(): array
    {
        return [
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ] + parent::_arguments();
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_numeric($input)) {
            $message = 'Value of type ' . gettype($input) . ' is not a number or value castable to a number.';
            return false;
        }

        return true;
    }

    protected function _unmarshal($input)
    {
        if (is_numeric($input)) {
            return $input + 0;
        } else {
            return $input;
        }
    }

}