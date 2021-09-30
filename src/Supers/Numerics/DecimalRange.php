<?php


namespace XUA\Supers\Numerics;


use XUA\Services\ExpressionService;
use XUA\Supers\Boolean;
use XUA\Exceptions\SuperValidationException;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property ?int integerLength
 * @method static SuperArgumentSignature A_integerLength() The Signature of: Argument `integerLength`
 * @property ?int fractionalLength
 * @method static SuperArgumentSignature A_fractionalLength() The Signature of: Argument `fractionalLength`
 * @property ?int base
 * @method static SuperArgumentSignature A_base() The Signature of: Argument `base`
 * @property bool unsigned
 * @method static SuperArgumentSignature A_unsigned() The Signature of: Argument `unsigned`
 * @property int|float min
 * @method static SuperArgumentSignature A_min() The Signature of: Argument `min`
 * @property int|float max
 * @method static SuperArgumentSignature A_max() The Signature of: Argument `max`
 */
class DecimalRange extends Decimal
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'unsigned' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'min' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30, 'nullable' => true]), false, null, false),
            'max' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        $this->unsigned = $this->min >= 0;
        parent::_validation($exception);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($this->min !== null and $input < $this->min) {
            $message = ExpressionService::get('errormessage.value.value.must.be.at.least.min', [
                'value' => $input,
                'min' => $this->min
            ]);
            return false;
        }

        if ($this->max !== null and $input > $this->max) {
            $message = ExpressionService::get('errormessage.value.value.must.be.at.most.max', [
                'value' => $input,
                'max' => $this->max
            ]);
            return false;
        }

        return true;
    }

    protected function _databaseType(): ?string
    {
        if ($this->min === null or $this->max === null) {
            return null;
        }

        $min = $this->min;
        $max = min($this->max, pow($this->base, $this->integerLength));
        $this->integerLength = floor(log(max(abs($min), abs($max)), $this->base) + 1);

        return parent::_databaseType();
    }
}