<?php


namespace Supers\Basics\Numerics;


use Supers\Basics\Boolean;
use XUA\Exceptions\SuperValidationException;
use XUA\Tools\SuperArgumentSignature;

class Decimal extends Number
{
    protected static function _arguments(): array
    {
        return [
            'integerLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'fractionalLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'base' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 2, false),
            'unsigned' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ] + parent::_arguments();
    }

    protected function _validation(): void
    {
        parent::_validation();

        if (!(2 <= $this->base and $this->base <= 16)) {
            throw new SuperValidationException("base must be in range [2, 16] but is $this->base");
        }

        if ($this->integerLength === null) {
            $length = $this->unsigned ? 64 : 63;
            $this->integerLength = (int)floor($length / log($this->base, 2));
        }

        if ($this->fractionalLength === null) {
            $this->fractionalLength = 0;
        }
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_numeric($input)) {
            $message = 'Value of type ' . gettype($input) . ' is not a number or value castable to a number.';
            return false;
        }

        if ($this->unsigned and $input < 0) {
            $message = "$input is less than zero therefore is not unsigned.";
            return false;
        }

        if ($input == floor($input)) {
            [$integerPart, $fractionalPart] = [$input, 0];
        } else {
            [$integerPart, $fractionalPart] = explode('.', (string)$input);
        }

        if ($integerPart != 0 and strlen($integerPart) > $this->integerLength) {
            $message = "Length of integer part $integerPart (" . strlen($integerPart) . ") is greater than maximum allowed length $this->integerLength.";
            return false;
        }

        if ($fractionalPart != 0 and strlen($fractionalPart) > $this->fractionalLength) {
            $message = "Length of fractional part $fractionalPart (" . strlen($fractionalPart) . ") is greater than maximum allowed length $this->fractionalLength.";
            return false;
        }

        return true;
    }

    protected function _databaseType(): ?string
    {
        $lengthCoefficient = log($this->base, 2);
        $BYTE = 8;

        $integerLength = ceil($this->integerLength * $lengthCoefficient);
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