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
 * @property array schemes
 * @method static SuperArgumentSignature A_schemes() The Signature of: Argument `schemes`
 */
class Url extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'schemes' => new SuperArgumentSignature(new Sequence(['type' => new Text([])]), true, null, false),
            'minLength' => new SuperArgumentSignature(new DecimalRange(['nullable' => true, 'min' => 0, 'max' => 2083, 'fractionalLength' => 0]), false, 0, false),
            'maxLength' => new SuperArgumentSignature(new DecimalRange(['nullable' => true, 'min' => 0, 'max' => 2083, 'fractionalLength' => 0]), false, 2083, false),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.url.format.is.not.valid');

        $startsWithValidSchema = false;
        foreach ($this->schemes as $scheme) {
            if (str_starts_with($input, $scheme)) {
                $startsWithValidSchema = true;
                break;
            }
        }

        return $startsWithValidSchema and filter_var($input, FILTER_VALIDATE_URL);
    }
}