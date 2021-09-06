<?php


namespace Supers\Customs;


use Services\Dataset\IranBankService;
use Services\XUA\ExpressionService;
use Supers\Basics\Numerics\Integer;
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
class Iban extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 26, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 34, true),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.incorrect.iban');

        return IranBankService::validateIban($input);
    }

    protected function _unmarshal($input): mixed
    {
        return strtoupper(str_replace(' ', '', $input));
    }
}