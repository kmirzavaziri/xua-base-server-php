<?php

namespace Xua\Core\Supers\Special;

use Closure;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Eves\Entity;
use Xua\Core\Eves\Super;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property callable getter
 * @property ?callable setter
 * @property ?string phpType
 */
class PhpVirtualField extends Super
{
    const getter = self::class . '::getter';
    const setter = self::class . '::setter';
    const phpType = self::class . '::phpType';

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
                            'passByReference' => false,
                        ],
                        [
                            'name' => null,
                            'type' => 'array',
                            'allowSubtype' => false,
                            'required' => true,
                            'checkDefault' => false,
                            'default' => null,
                            'passByReference' => false,
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
        $typeToString = function (ReflectionNamedType $type) {
            return (($type->allowsNull() and $type->getName() != 'null') ? '?' : '') . ($type->isBuiltin() ? '' : '\\') . $type->getName();
        };
        $returnType = (new ReflectionFunction(Closure::fromCallable($this->getter)))->getReturnType();
        if ($returnType === null) {
            return 'mixed';
        } elseif (is_a($returnType, ReflectionNamedType::class)) {
            return $typeToString($returnType);
        } elseif (is_a($returnType, ReflectionUnionType::class)) {
            return implode('|', array_map($typeToString, $returnType->getTypes()));
        } elseif (is_a($returnType, ReflectionIntersectionType::class)) {
            return implode('&', array_map($typeToString, $returnType->getTypes()));
        } else {
            return 'mixed';
        }
    }
}