<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property int marshalFlags
 * @property null|\Xua\Core\Eves\Super keyType
 * @property null|\Xua\Core\Eves\Super valueType
 * @property ?int minSize
 * @property ?int maxSize
 */
class Map extends Json
{
    const nullable = self::class . '::nullable';
    const marshalFlags = self::class . '::marshalFlags';
    const keyType = self::class . '::keyType';
    const valueType = self::class . '::valueType';
    const minSize = self::class . '::minSize';
    const maxSize = self::class . '::maxSize';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::keyType, false, null,
                new Instance([Instance::of => Super::class, Instance::nullable => true])
            ),
            Signature::new(false, static::valueType, false, null,
                new Instance([Instance::of => Super::class, Instance::nullable => true])
            ),
            Signature::new(false, static::minSize, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(false, static::maxSize, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input) and !is_object($input)) {
            $message = gettype($input) . " is neither array nor object."; // @TODO message from dict
            return false;
        }

        if (is_object($input)) {
            $input = (array)$input;
        }

        $size = count($input);

        if ($this->minSize !== null and $size < $this->minSize) {
            $message = "Size of input ($size) must be at least $this->minSize."; // @TODO message from dict
            return false;
        }

        if ($this->maxSize !== null and $size > $this->maxSize) {
            $message = "Size of input ($size) must be at most $this->maxSize."; // @TODO message from dict
            return false;
        }

        if ($this->keyType != null) {
            foreach ($input as $key => $value) {
                if(!$this->keyType->explicitlyAccepts($key, $m)) {
                    $message = [$key => $m];
                    return false;
                }
            }
        }
        if ($this->valueType != null) {
            foreach ($input as $key => $value) {
                if(!$this->valueType->explicitlyAccepts($value, $m)) {
                    $message = [$key => $m];
                    return false;
                }
            }
        }

        return true;
    }

    protected function _nestedMarshal($input): mixed
    {
        if ($this->valueType) {
            foreach ($input as $key => $value) {
                $input[$key] = $value === null
                    ? null
                    : $this->valueType->nestedMarshal($value);
            }
        }
        return $input;
    }

    protected function _nestedUnmarshal($input): mixed
    {
        if (!is_array($input)) {
            return $input;
        }
        if ($this->valueType) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->valueType->nestedUnmarshal($value);
            }
        }
        return $input;
    }
}