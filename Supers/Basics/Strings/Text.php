<?php


namespace Supers\Basics\Strings;


use Supers\Basics\Boolean;
use Supers\Basics\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

class Text extends Super
{
    protected static function _arguments(): array
    {
        return [
                'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
                'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, 65_535, false),
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ] + parent::_arguments();
    }

    protected function _validation(): void
    {
        if ($this->minLength !== null and $this->maxLength !== null and $this->minLength > $this->maxLength) {
            throw new SuperValidationException("Min length $this->minLength cannot be greater than max length $this->maxLength");
        }
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_string($input)) {
            $message = gettype($input) . " is not string or castable to string.";
            return false;
        }

        $length = strlen($input);

        if ($this->minLength !== null and $length < $this->minLength) {
            $message = "Length of $input ($length) must be at least $this->minLength.";
            return false;
        }

        if ($this->maxLength !== null and $length > $this->maxLength) {
            $message = "Length of $input ($length) must be at most $this->maxLength.";
            return false;
        }

        return true;
    }

    protected function _unmarshal($input)
    {
        if (is_scalar($input)) {
            return (string)$input;
        }

        return $input;
    }

    protected function _databaseType(): ?string
    {
        if ($this->maxLength <= pow(2, 16) - 1) {
            $type = "VARCHAR($this->maxLength)";
        } elseif ($this->maxLength <= pow(2, 24) - 1) {
            $type = "MEDIUMTEXT";
        } elseif ($this->maxLength <= pow(2, 32) - 1) {
            $type = "LONGTEXT";
        } else {
            return null;
        }
        $nullExpression = $this->nullable ? '' : ' NOT NULL';

        return "$type$nullExpression";
    }
}