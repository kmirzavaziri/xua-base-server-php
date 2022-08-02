<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Eves\Super;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property callable getter
 * @property ?string phpType
 */
class DatabaseVirtualField extends Super
{
    const getter = self::class . '::getter';
    const phpType = self::class . '::phpType';

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
            Signature::new(false, static::phpType, false, null,
                new Text([
                    Text::nullable => true
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

    protected function _phpType(): string
    {
        if ($this->phpType) {
            return $this->phpType;
        }
        return 'mixed';
    }
}