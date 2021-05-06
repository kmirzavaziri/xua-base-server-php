<?php


namespace Supers\Basics\EntitySupers;


use Supers\Basics\Highers\Callback;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

/**
 * @property callable getter
 */
class DatabaseVirtualField extends Super
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'getter' => new SuperArgumentSignature(new Callback([
                'parameters' => [
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