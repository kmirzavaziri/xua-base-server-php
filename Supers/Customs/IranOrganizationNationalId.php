<?php


namespace Supers\Customs;


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
class IranOrganizationNationalId extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 11, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 11, true),
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

        $message = ExpressionService::get('errormessage.incorrect.organization.national.id');

        if(!preg_match('/^[0-9]{11}$/', $input)) {
            return false;
        }

        if(substr($input, 3, 6) == 0) {
            return false;
        }

        $addend = $input[9] + 2;
        $coefficient = [29, 27, 23, 19, 17, 29, 27, 23, 19, 17];

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (($addend + $input[$i]) * $coefficient[$i]) % 11;
        }
        $sum = $sum % 11;

        if($sum == 10) {
            $sum = 0;
        }

        if($sum != $input[10]) {
            return false;
        }

        return true;
    }
}