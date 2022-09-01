<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Super;
use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property callable conditionGenerator
 */
class ConditionScheme extends Super
{
    const conditionGenerator = self::class . '::conditionGenerator';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::conditionGenerator, true, null,
                new Callback([
                    Callback::parameters => [
                        [
                            'name' => null,
                            'type' => 'mixed',
                            'allowSubtype' => true,
                            'required' => true,
                            'checkDefault' => false,
                            'default' => null,
                            'passByReference' => false,
                        ],
                    ],
                    Callback::returnType => Condition::class,
                ])
            ),
        ]);
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        return 'null';
    }

    protected function _predicate($input, array|string|null &$message = null): bool
    {
        return true;
    }
}