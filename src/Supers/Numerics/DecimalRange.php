<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Boolean;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property ?int integerLength
 * @property ?int fractionalLength
 * @property ?int base
 * @property bool unsigned
 * @property null|int|float min
 * @property null|int|float max
 */
class DecimalRange extends Decimal
{
    const nullable = self::class . '::nullable';
    const integerLength = self::class . '::integerLength';
    const fractionalLength = self::class . '::fractionalLength';
    const base = self::class . '::base';
    const unsigned = self::class . '::unsigned';
    const min = self::class . '::min';
    const max = self::class . '::max';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(true, static::unsigned, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::min, false, null,
                new Decimal([Decimal::integerLength => 255, Decimal::fractionalLength => 30, Decimal::nullable => true])
            ),
            Signature::new(false, static::max, false, null,
                new Decimal([Decimal::integerLength => 255, Decimal::fractionalLength => 30, Decimal::nullable => true])
            ),
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