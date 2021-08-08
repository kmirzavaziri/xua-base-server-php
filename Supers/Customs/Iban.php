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
class Iban extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 26, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 34, true),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.incorrect.iban');

        if(strlen($input) < 2) {
            return false;
        }
        $country = substr($input,0, 2);

        $lengths = ['AL' => 28, 'AD' => 24, 'AT' => 20, 'AZ' => 28, 'BH' => 22, 'BE' => 16, 'BA' => 20, 'BR' => 29, 'BG' => 22, 'CR' => 21, 'HR' => 21, 'CY' => 28, 'CZ' => 24, 'DK' => 18, 'DO' => 28, 'EE' => 20, 'FO' => 18, 'FI' => 18, 'FR' => 27, 'GE' => 22, 'DE' => 22, 'GI' => 23, 'GR' => 27, 'GL' => 18, 'GT' => 28, 'HU' => 28, 'IS' => 26, 'IE' => 22, 'IL' => 23, 'IT' => 27, 'JO' => 30, 'KZ' => 20, 'KW' => 30, 'LV' => 21, 'LB' => 28, 'LI' => 21, 'LT' => 20, 'LU' => 20, 'MK' => 19, 'MT' => 31, 'MR' => 27, 'MU' => 30, 'MC' => 27, 'MD' => 24, 'ME' => 22, 'NL' => 18, 'NO' => 15, 'PK' => 24, 'PS' => 29, 'PL' => 28, 'PT' => 25, 'QA' => 29, 'RO' => 24, 'SM' => 27, 'SA' => 24, 'RS' => 22, 'SK' => 24, 'SI' => 19, 'ES' => 24, 'SE' => 24, 'CH' => 21, 'TN' => 24, 'TR' => 26, 'AE' => 23, 'GB' => 22, 'VG' => 24, 'DZ' => 26, 'AO' => 25, 'BJ' => 28, 'BF' => 28, 'BI' => 16, 'CV' => 25, 'CM' => 27, 'CF' => 27, 'TD' => 27, 'KM' => 27, 'CG' => 27, 'CI' => 28, 'DJ' => 27, 'GQ' => 27, 'GA' => 27, 'GW' => 25, 'HN' => 28, 'IR' => 26, 'MG' => 27, 'ML' => 28, 'MA' => 28, 'MZ' => 25, 'NI' => 32, 'NE' => 28, 'SN' => 28, 'TG' => 28];

        if (!isset($lengths[$country]) or $lengths[$country] != strlen($input)) {
            return false;
        }

        $chars = str_split(substr($input, 4) . substr($input,0,4));
        $checkString = "";
        foreach($chars AS $char){
            if(is_numeric($char)){
                $checkString .= $char;
            } else {
                $checkString .= ord($char) - 55;
            }
        }

        if(bcmod($checkString, '97') != 1) {
            return false;
        }

        return true;
    }

    protected function _unmarshal($input): mixed
    {
        return strtoupper(str_replace(' ', '', $input));
    }
}