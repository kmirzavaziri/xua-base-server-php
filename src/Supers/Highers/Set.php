<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Eves\Super;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\SetInstance;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property array values
 */
class Set extends Super
{
    const nullable = self::class . '::nullable';
    const values = self::class . '::values';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::values, true, null,
                new Sequence([Sequence::type => new Text([]), Sequence::minLength => 1])
            )
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_object($input) or get_class($input) != SetInstance::class) {
            $message = ExpressionService::getXua('supers.highers.set.error_message.invalid');
            return false;
        }

        $validValues = SetInstance::fromList($this->values);
        $invalidValues = $input->minus($validValues);

        if (!$invalidValues->empty()) {
            $message = ExpressionService::getXua('supers.highers.set.error_message.invalid_values', [
                'values' => $invalidValues->toList(),
                'set' => $validValues->toList(),
            ]);
            return false;
        }

        return true;
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        return $input->toString();
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        return is_string($input) ? SetInstance::fromString($input) : $input;
    }

    protected function _marshal(mixed $input): mixed
    {
        return $this->_marshalDatabase($input);
    }

    protected function _unmarshal(mixed $input): mixed
    {
        return $this->_unmarshalDatabase($input);
    }

    protected function _databaseType(): ?string
    {
        if (!$this->values) {
            return null;
        }

        $nullExpression = $this->nullable ? ' NULL' : ' NOT NULL';
        return "SET('" . implode("','", $this->values) . "')$nullExpression";
    }
}