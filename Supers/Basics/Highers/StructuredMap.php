<?php


namespace Supers\Basics\Highers;


use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Symbol;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

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
            $message = "Unknown keys " . implode(', ', $unknownKeys) . ".";
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
                $message = "Key '$key' is missing.";
                return false;
            }
        }

        return true;
    }
}