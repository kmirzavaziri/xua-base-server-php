<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property array|object structure
 * @method static SuperArgumentSignature A_structure() The Signature of: Argument `structure`
 */
class StructuredMap extends Json
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'structure' => new SuperArgumentSignature(new Map(['keyType' => new Symbol([]), 'valueType' => new Instance(['of' => Super::class, 'nullable' => true])]), true, null, false),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input) and !is_object($input)) {
            $message = gettype($input) . " is neither array nor object.";
            return false;
        }

        if (is_object($input)) {
            $input = (array)$input;
        }

        $unknownKeys = array_diff(array_keys($input), array_keys($this->structure));
        if ($unknownKeys) {
            if (count($unknownKeys) == 1) {
                $message = ExpressionService::get('errormessage.unknown.key.key', [
                    'key' => implode('', $unknownKeys)
                ]);
            } else {
                $message = ExpressionService::get('errormessage.unknown.keys.keys', [
                    'keys' => implode(ExpressionService::get('comma.separator'), $unknownKeys)
                ]);
            }
            return false;
        }

        foreach ($this->structure as $key => $type) {
            /** @var ?Super $type */

            if (in_array($key, array_keys($input))) {
                if ($type !== null and !$type->_predicate($input[$key], $messages)) {
                    $message[$key] = $messages;
                    return false;
                }
            } else {
                $message = ExpressionService::get('errormessage.key.key.is.missing', ['key' => $key]);
                return false;
            }
        }

        return true;
    }
}