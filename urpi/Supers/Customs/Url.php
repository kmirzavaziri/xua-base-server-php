<?php


namespace Supers\Customs;


use Supers\Basics\Strings\Text;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Url extends Text
{
    // @TODO add types http/https, mailto, telegram, etc.
    protected function _predicate($input, string &$message = null): bool
    {
        $message = 'Not implemented yet';
        return false;
    }
}