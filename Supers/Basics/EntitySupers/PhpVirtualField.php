<?php


namespace Supers\Basics\EntitySupers;


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
                'parameters' => [
                    [
                        'name' => 'entity',
                        'type' => Entity::class,
                        'required' => true,
                        'checkDefault' => false,
                        'default' => null,
                        'passByReference' => false,
                    ],
                    [
                        'name' => 'param',
                        'type' => 'array',
                        'required' => true,
                        'checkDefault' => false,
                        'default' => null,
                        'passByReference' => false,
                    ],
                ]
            ]), true, null, false),
            'setter' => new SuperArgumentSignature(new Callback([
                'nullable' => true,
                'parameters' => [
                    [
                        'name' => 'entity',
                        'type' => Entity::class,
                        'required' => true,
                        'checkDefault' => false,
                        'default' => null,
                        'passByReference' => true,
                    ],
                    [
                        'name' => 'param',
                        'type' => 'array',
                        'required' => true,
                        'checkDefault' => false,
                        'default' => null,
                        'passByReference' => false,
                    ],
                    [
                        'name' => 'value',
                        'type' => 'mixed',
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
}