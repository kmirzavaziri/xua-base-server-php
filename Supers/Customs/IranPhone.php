<?php


namespace Supers\Customs;


use Services\XUA\ExpressionService;
use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Exceptions\SuperValidationException;
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
        return array_merge(parent::_argumentSignatures(), [
            'type' => new SuperArgumentSignature(new Enum(['values' => ['cellphone', 'landline', 'both']]), true, null, false),
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 13, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 13, true),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        $cellPhoneValidPrefixes = [
            '901', '902', '903', '904', '905', '930', '933', '935', '936', '937', '938', '939', '941', /* ایرانسل */
            '910', '911', '912', '913', '914', '915', '916', '917', '918', '919', /* همراه اول */
            '920', '921', '931', '932', '934', '999' /* سایر */
        ];
        $validCellphonePrefixesPattern = '(' . implode('|', $cellPhoneValidPrefixes) . ')';

        $landlineValidPrefixes = [
            '41', /* آذربایجان شرقی */      '44', /* آذربایجان غربی */ '45', /* اردبیل */
            '31', /* اصفهان */              '26', /* البرز */          '84', /* ایلام */
            '77', /* بوشهر */               '21', /* تهران */          '56', /* خراسان جنوبی */
            '51', /* خراسان رضوی */         '58', /* خراسان شمالی */   '61', /* خوزستان */
            '24', /* زنجان */               '23', /* سمنان */          '54', /* سیستان و بلوچستان */
            '71', /* فارس */                '28', /* قزوین */          '25', /* قم */
            '66', /* لرستان */              '11', /* مازندران */       '86', /* مرکزی */
            '76', /* هرمزگان */             '81', /* همدان */          '38', /* چهارمحال و بختیاری */
            '87', /* کردستان */             '34', /* کرمان */          '83', /* کرمانشاه */
            '74', /* کهگیلویه و بویراحمد */ '17', /* گلستان */         '13', /* گیلان */
            '35', /* یزد */
        ];
        $validLandlinePrefixesPattern = '(' . implode('|', $landlineValidPrefixes) . ')[0-9]';

        switch ($this->type) {
            case 'cellphone':
                $message = ExpressionService::get('errormessage.cellphone.format.is.not.valid');
                return preg_match('/^\+98' . $validCellphonePrefixesPattern . '[0-9]{7}$/', $input);
            case 'landline':
                $message = ExpressionService::get('errormessage.landline.format.is.not.valid');
                return preg_match('/^\+98' . $validLandlinePrefixesPattern . '[0-9]{7}$/', $input);
            case 'both':
                $message = ExpressionService::get('errormessage.both.format.is.not.valid');
                return
                    preg_match('/^\+98' . $validCellphonePrefixesPattern . '[0-9]{7}$/', $input) or
                    preg_match('/^\+98' . $validLandlinePrefixesPattern . '[0-9]{7}$/', $input);
            default:
                $message = ExpressionService::get('errormessage.not.implemented.yet');
                return false;
        }
    }

    protected function _unmarshal($input): mixed
    {
        $input = parent::_unmarshal($input);
        return match ($this->type) {
            'cellphone' => strlen($input) < 9 ? $input : '+989' . substr($input, -9),
            'landline', 'both' => strlen($input) < 10 ? $input : '+98' . substr($input, -10),
            default => $input,
        };
    }
}