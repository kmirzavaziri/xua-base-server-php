<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\SuperArgumentSignature;

/**
 * @property \Xua\Core\Eves\Super type
 */
class Nullable extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
                'type' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => false]), true, null, false),
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