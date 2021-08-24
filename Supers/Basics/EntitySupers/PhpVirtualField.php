<?php


namespace Supers\Basics\EntitySupers;

use Closure;
use ReflectionFunction;
use Supers\Basics\Highers\Callback;
use XUA\Entity;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property callable getter
 * @method static SuperArgumentSignature A_getter() The Signature of: Argument `getter`
 * @property ?callable setter
 * @method static SuperArgumentSignature A_setter() The Signature of: Argument `setter`
 */
class PhpVirtualField extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'getter' => new SuperArgumentSignature(new Callback([
                'nullable' => false,
                'parameters' => [
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
            ]), true, null, false),
            'setter' => new SuperArgumentSignature(new Callback([
                'nullable' => true,
                // @TODO must set return to void
                'parameters' => [
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
            ]), false, null, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        return true;
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        $returnType = (new ReflectionFunction(Closure::fromCallable($this->getter)))->getReturnType();
        return $returnType ? '\\' . $returnType : 'mixed';
    }
}