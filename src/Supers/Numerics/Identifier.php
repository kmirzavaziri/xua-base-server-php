<?php

namespace Xua\Core\Supers\Numerics;

use Xua\Core\Eves\Super;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Signature\SuperArgumentSignature;

/**
 * @property string bytes
 * @method static SuperArgumentSignature A_bytes() The Signature of: Argument `bytes`
 */
class Identifier extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'bytes' => new SuperArgumentSignature(new Enum(['values' => ["1", "2", "3", "4", "8"], 'nullable' => false]), false, 4, false),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return (new Decimal(['nullable' => false, 'unsigned' => true, 'integerLength' => 8 * $this->bytes, 'base' => 2]))->_predicate($input, $message);
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