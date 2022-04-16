<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property int marshalFlags
 * @property null|\Xua\Core\Eves\Super type
 * @property ?int minLength
 * @property ?int maxLength
 */
class Sequence extends Json
{
    const nullable = self::class . '::nullable';
    const marshalFlags = self::class . '::marshalFlags';
    const type = self::class . '::type';
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::type, false, null,
                new Instance([Instance::of => Super::class, Instance::nullable => true])
            ),
            Signature::new(false, static::minLength, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(false, static::maxLength, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
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

        if (!is_array($input)) {
            $message = gettype($input) . " is not array."; // @TODO message from dict
            return false;
        }

        $length = count($input);
        $expectedKeys = range(0, $length - 1);
        $arrayKeys = array_keys($input);
        if ($input != [] and $arrayKeys !== $expectedKeys) {
            $expectedKeysString = $length > 3 ? "0, ..., " . ($length - 1) : implode(', ', $expectedKeys);
            $message = "Array keys (" . implode(', ', $arrayKeys) . ") are not the keys ($expectedKeysString).";
            return false;
        }

        if ($this->minLength !== null and $length < $this->minLength) {
            $message = "Length of input ($length) must be at least $this->minLength.";
            return false;
        }

        if ($this->maxLength !== null and $length > $this->maxLength) {
            $message = "Length of input ($length) must be at most $this->maxLength.";
            return false;
        }

        $message = [];
        if ($this->type != null) {
            foreach ($input as $i => $item) {
                if (!$this->type->_predicate($item, $itemMessage)) {
                    $message[$i] = $itemMessage;
                    return false;
                }
            }
        }


        return true;
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . ($this->type->_phpType() != 'mixed' ? $this->type->_phpType() . '[]' : 'array');
    }
}