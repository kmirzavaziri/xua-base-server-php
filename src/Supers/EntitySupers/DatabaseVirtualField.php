<?php


namespace XUA\Supers\EntitySupers;


use XUA\Supers\Highers\Callback;
use XUA\Eves\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property callable getter
 * @method static SuperArgumentSignature A_getter() The Signature of: Argument `getter`
 */
class DatabaseVirtualField extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'getter' => new SuperArgumentSignature(new Callback([
                'parameters' => [
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
            ]), true, null, false),
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