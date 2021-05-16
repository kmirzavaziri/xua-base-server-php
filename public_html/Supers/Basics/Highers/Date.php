<?php


namespace Supers\Basics\Highers;



use ReflectionFunction;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Symbol;
use Supers\Basics\Strings\Text;
use Supers\Basics\Trilean;
use Supers\Basics\Universal;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Date extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Not implemented yet';
        return false;
    }
}