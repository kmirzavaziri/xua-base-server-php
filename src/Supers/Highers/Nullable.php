<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property \Xua\Core\Eves\Super type
 */
class Nullable extends Super
{
    const type = self::class . '::type';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::type, true, null,
                new Instance([Instance::of => Super::class])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($input === null) {
            return true;
        }
        return $this->type->_predicate($input, $message);
    }

    protected function _nestedMarshal(mixed $input): mixed
    {
        return $input === null ? 'null' : $this->type->_nestedMarshal($input);
    }

    protected function _nestedUnmarshal(mixed $input): mixed
    {
        return $this->type->_nestedUnmarshal($input);
    }

    protected function _marshal(mixed $input): mixed
    {
        return $input === null ? 'null' : $this->type->_marshal($input);
    }

    protected function _unmarshal(mixed $input): mixed
    {
        return $this->type->_unmarshal($input);
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        return $input === null ? 'null' : $this->type->_marshalDatabase($input);
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        return $this->type->_unmarshalDatabase($input);
    }

    protected function _phpType(): string
    {
        return 'null|' . $this->type->_phpType();
    }
}