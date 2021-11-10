<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property callable getter
 */
class DatabaseVirtualField extends Super
{
    const getter = self::class . '::getter';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::getter, true, null,
                new Callback([
                    Callback::parameters => [
                        [
                            'name' => 'param',
                            'type' => 'array',
                            'allowSubtype' => true,
                            'required' => true,
                            'checkDefault' => false,
                            'default' => null,
                            'passByReference' => false,
                        ],
                    ]
                ])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return true;
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }
}