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
            'type' => new SuperArgumentSignature(new Enum(['values' => ['cellphone', 'landline', 'fax']]), true, null, false),
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 65_535, true),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if ($this->type == 'cellphone') {
            $this->maxLength = 13;
        }
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        switch ($this->type) {
            case 'cellphone':
                $message = ExpressionService::get('errormessage.cellphone.format.is.not.valid');
                return strlen($input) == 13 and str_starts_with($input, '+989') and !preg_match('/[^0-9+\- ()]/', $input);
            case 'landline':
            case 'fax':
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
            'landline' => $input,
            'fax' => $input,
            default => $input,
        };
    }
}