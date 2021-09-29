<?php


namespace XUA\Supers\Strings;


use XUA\Supers\Boolean;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property string pattern
 * @method static SuperArgumentSignature A_pattern() The Signature of: Argument `pattern`
 * @property bool allowEmpty
 * @method static SuperArgumentSignature A_allowEmpty() The Signature of: Argument `allowEmpty`
 */
class Symbol extends Regex
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'allowEmpty' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            'pattern' => new SuperArgumentSignature(new Text([]), false, '/^[a-zA-Z_][a-zA-Z_0-9]*$/', true)
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($input === '' and $this->allowEmpty) {
            return true;
        }
        return parent::_predicate($input, $message);
    }
}