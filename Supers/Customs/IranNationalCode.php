<?php


namespace Supers\Customs;


use Services\XUA\ExpressionService;
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
class IranNationalCode extends Text
{
    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.incorrect.national.code');

        if(!preg_match('/^[0-9]{10}$/', $input)) {
            return false;
        }
        for ($i = 0; $i < 10; $i++) {
            if(preg_match('/^'.$i.'{10}$/', $input)) {
                return false;
            }
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((10-$i) * $input[$i]) % 11;
        }
        $sum = $sum % 11;

        if($sum != ($sum < 2 ? $input[9] : 11- $input[9])) {
            return false;
        }

        return true;
    }
}