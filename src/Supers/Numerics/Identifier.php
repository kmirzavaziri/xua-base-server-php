<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Eves\Super;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property string bytes
 * @property null|\Xua\Core\Eves\Super type
 * @property bool autoIncrement
 */
class Identifier extends Super
{
    const bytes = self::class . '::bytes';
    const type = self::class . '::type';
    const autoIncrement = self::class . '::autoIncrement';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::bytes, false, 4,
                new Enum([Enum::values => ["1", "2", "3", "4", "8"]])
            ),
            Signature::new(true, static::type, false, null,
                new Instance([Instance::nullable => true, Instance::of => Super::class])
            ),
            Signature::new(false, static::autoIncrement, false, true,
                new Boolean([])
            ),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        $this->type = (new Decimal([Decimal::unsigned => true, Decimal::integerLength => 8 * $this->bytes, Decimal::base => 2]));
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return $this->type->_predicate($input, $message);
    }

    protected function _marshal(mixed $input): mixed
    {
        return $this->type->_marshal($input);
    }

    protected function _ummarshal(mixed $input): mixed
    {
        return $this->type->_ummarshal($input);
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        return $this->type->_marshalDatabase($input);
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        return $this->type->_unmarshalDatabase($input);
    }

    protected function _databaseType(): ?string
    {
        return match ($this->bytes) {
            "1" => 'TINYINT',
            "2" => 'SMALLINT',
            "3" => 'MEDIUMINT',
            "4" => 'INT',
            "8" => 'BIGINT',
        } . ' UNSIGNED NOT NULL' . ($this->autoIncrement ? ' AUTO_INCREMENT' : '');
    }

    protected function _phpType(): string
    {
        return 'int';
    }
}