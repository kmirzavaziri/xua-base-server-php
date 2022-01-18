<?php

namespace Xua\Core\Supers\Strings;

use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 */
class Text extends Super
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::minLength, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(false, static::maxLength, false, 65_535,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if ($this->minLength !== null and $this->maxLength !== null and $this->minLength > $this->maxLength) {
            $exception->setError('maxLength', "Max length $this->maxLength cannot be less than min length $this->minLength");
        }
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_string($input)) {
            $message = gettype($input) . " is not string.";
            return false;
        }

        $length = strlen($input);

        if ($this->minLength !== null and $length < $this->minLength) {
            $message = "Length of '$input' ($length) must be at least $this->minLength.";
            return false;
        }

        if ($this->maxLength !== null and $length > $this->maxLength) {
            $message = "Length of '$input' ($length) must be at most $this->maxLength.";
            return false;
        }

        return true;
    }

    protected function _unmarshal($input): mixed
    {
        if (is_scalar($input)) {
            return (string)$input;
        }

        return $input;
    }

    protected function _databaseType(): ?string
    {
        if ($this->maxLength <= pow(2, 10) - 1) {
            $type = "VARCHAR($this->maxLength)";
        } elseif ($this->maxLength <= pow(2, 16) - 1) {
            $type = "TEXT";
        } elseif ($this->maxLength <= pow(2, 24) - 1) {
            $type = "MEDIUMTEXT";
        } elseif ($this->maxLength <= pow(2, 32) - 1) {
            $type = "LONGTEXT";
        } else {
            return null;
        }
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';

        return "$type$nullExpression";
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'string';
    }
}