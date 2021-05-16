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
 * @property ?string type
 * @method static SuperArgumentSignature A_type() The Signature of: Argument `type`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class File extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'type' => new SuperArgumentSignature(new Enum([
                'values' => ['image', 'document', 'archive', 'pdf', 'word', 'text', 'video'],
                'nullable' => true,
            ]), false, null, false),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Not implemented yet';
        return false;
    }
}