<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Eves\Super;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property string bytes
 */
class Identifier extends Super
{
    const bytes = self::class . '::bytes';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::bytes, false, 4,
                new Enum([Enum::values => ["1", "2", "3", "4", "8"], Enum::nullable => false])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return (new Decimal([Decimal::nullable => false, Decimal::unsigned => true, Decimal::integerLength => 8 * $this->bytes, Decimal::base => 2]))->_predicate($input, $message);
    }

    protected function _databaseType(): ?string
    {
        return match ($this->bytes) {
            "1" => 'TINYINT',
            "2" => 'SMALLINT',
            "3" => 'MEDIUMINT',
            "4" => 'INT',
            "8" => 'BIGINT',
        } . ' UNSIGNED NOT NULL AUTO_INCREMENT';
    }

    protected function _phpType(): string
    {
        return 'int';
    }
}