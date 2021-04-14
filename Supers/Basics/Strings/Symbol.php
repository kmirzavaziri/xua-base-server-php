<?php


namespace Supers\Basics\Strings;


use Supers\Basics\Boolean;
use Supers\Basics\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

class Symbol extends Regex
{
    protected static function _arguments(): array
    {
        return [
            'pattern' => new SuperArgumentSignature(new Text([]), false, '/^[a-zA-Z_][a-zA-Z_0-9]*$/', true)
        ] + parent::_arguments();
    }
}