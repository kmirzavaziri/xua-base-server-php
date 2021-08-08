<?php


namespace Supers\Customs;


use Services\XUA\ExpressionService;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\DecimalRange;
use Supers\Basics\Strings\Text;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property null|int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property null|int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Email extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minLength' => new SuperArgumentSignature(new DecimalRange(['nullable' => true, 'min' => 0, 'max' => 320]), false, 0, false),
            'maxLength' => new SuperArgumentSignature(new DecimalRange(['nullable' => true, 'min' => 0, 'max' => 320]), false, 320, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.email.format.is.not.valid');
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}