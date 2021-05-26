<?php


namespace Supers\Customs;


use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property string type
 * @method static SuperArgumentSignature A_type() The Signature of: Argument `type`
 */
class IranPhone extends Text
{
    protected static function _argumentSignatures(): array
    {
        // @TODO make maxLength and minLength constant
        return array_merge(parent::_argumentSignatures(), [
            'type' => new SuperArgumentSignature(new Enum(['values' => ['cellphone', 'landline', 'fax']]), true, null, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Not implemented yet';
        return false;
    }
}