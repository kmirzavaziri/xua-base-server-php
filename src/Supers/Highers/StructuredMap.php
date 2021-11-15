<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property int marshalFlags
 * @property array structure
 */
class StructuredMap extends Json
{
    const nullable = self::class . '::nullable';
    const structure = self::class . '::structure';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::structure, true, null,
                new Map([Map::keyType => new Symbol([]), Map::valueType => new Instance([Instance::of => Super::class, Instance::nullable => true])])
            ),
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
                $message = ExpressionService::get('xua.supers.highers.structured_map.error_message.unknown_key', [
                    'key' => $unknownKeys
                ]);
            } else {
                $message = ExpressionService::get('xua.supers.highers.structured_map.error_message.unknown_keys', [
                    'keys' => $unknownKeys
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
                $message = ExpressionService::get('xua.supers.highers.structured_map.error_message.key_is_missing', ['key' => $key]);
                return false;
            }
        }

        return true;
    }
}