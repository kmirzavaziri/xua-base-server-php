<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
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
            'min' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30]), false, null, false),
            'max' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30]), false, null, false),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        $this->unsigned = $this->min >= 0;
        parent::_validation($exception);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($input < $this->min) {
            $message = "Value $input must be at least $this->min.";
            return false;
        }

        if ($input > $this->max) {
            $message = "Value $input must be at most $this->max.";
            return false;
        }

        return true;
    }

    protected function _databaseType(): ?string
    {
        $min = $this->min;
        $max = min($this->max, pow($this->base, $this->integerLength));

        $lengthCoefficient = log($this->base, 2);
        $BYTE = 8;

        $integerLength = floor(log(max(abs($min), abs($max)), 2) + 1);
        $fractionalLength = ceil($this->fractionalLength * $lengthCoefficient);

        if (!$fractionalLength) {
            if (!$this->unsigned) {
                $integerLength += 1;
            }
            if ($integerLength <= 1 * $BYTE) {
                $type = "TINYINT";
            } elseif ($integerLength <= 2 * $BYTE) {
                $type = "SMALLINT";
            } elseif ($integerLength <= 3 * $BYTE) {
                $type = "MEDIUMINT";
            } elseif ($integerLength <= 4 * $BYTE) {
                $type = "INT";
            } elseif ($integerLength <= 8 * $BYTE) {
                $type = "BIGINT";
            } else {
                return null;
            }
            $signExpression = $this->unsigned ? ' UNSIGNED' : '';

        } else {
            $length = $this->integerLength + $this->fractionalLength;
            if ($length <= 65 and $this->fractionalLength <= 30) {
                $type = "DECIMAL($length, $this->fractionalLength)";
            } elseif ($length <= 255 and $this->fractionalLength <= 30) {
                $type = "DOUBLE($length, $this->fractionalLength)";
            } else {
                return null;
            }
            $signExpression = '';
        }

        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "$type$signExpression$nullExpression";
    }
}