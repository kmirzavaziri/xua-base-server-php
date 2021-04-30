<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
use XUA\Tools\SuperArgumentSignature;

/**
 * @property bool nullable
 * @property ?int integerLength
 * @property ?int fractionalLength
 * @property ?int base
 * @property bool unsigned
 * @property int|float min
 * @property int|float max
 */
class DecimalRange extends Decimal
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'unsigned' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'min' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30]), false, null, false),
            'max' => new SuperArgumentSignature(new Decimal(['integerLength' => 255, 'fractionalLength' => 30]), false, null, false),
        ]);
    }

    protected function _validation(): void
    {
        $this->unsigned = $this->min >= 0;
        parent::_validation();
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

        $nullExpression = $this->nullable ? '' : ' NOT NULL';
        return "$type$signExpression$nullExpression";
    }
}