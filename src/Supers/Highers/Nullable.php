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
                new Instance([Instance::of => Super::class, Instance::nullable => false])
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

    protected function _phpType(): string
    {
        return 'null|' . $this->type->_phpType();
    }
}