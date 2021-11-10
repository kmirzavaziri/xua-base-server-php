<?php

namespace Xua\Core\Supers\Special;

use Closure;
use ReflectionFunction;
use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Eves\Entity;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property callable getter
 * @property ?callable setter
 */
class PhpVirtualField extends Super
{
    const getter = self::class . '::getter';
    const setter = self::class . '::setter';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::getter, true, null,
                new Callback([
                    Callback::nullable => false,
                    Callback::parameters => [
                        [
                            'name' => null,
                            'type' => Entity::class,
                            'allowSubtype' => true,
                            'required' => true,
                            'checkDefault' => false,
                            'default' => null,
                            'passByReference' => false,
                        ],
                    ]
                ])
            ),
            Signature::new(false, static::setter, false, null,
                new Callback([
                    Callback::nullable => true,
                    // @TODO must set return to void
                    Callback::parameters => [
                        [
                            'name' => null,
                            'type' => Entity::class,
                            'allowSubtype' => true,
                            'required' => true,
                            'checkDefault' => false,
                            'default' => null,
                            'passByReference' => true,
                        ],
                        [
                            'name' => null,
                            'type' => null,
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

    protected function _phpType(): string
    {
        // @TODO fix this for cases like ?ClassName
        $returnType = (new ReflectionFunction(Closure::fromCallable($this->getter)))->getReturnType();
        return $returnType ? (class_exists($returnType) ? '\\' . $returnType : $returnType) : 'mixed';
    }
}