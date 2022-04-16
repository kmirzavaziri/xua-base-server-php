<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\JsonService;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property int marshalFlags
 */
class Json extends Super
{
    const nullable = self::class . '::nullable';
    const marshalFlags = self::class . '::marshalFlags';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::marshalFlags, false, JSON_UNESCAPED_UNICODE,
                new Integer([])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input)) {
            $message = gettype($input) . " is not an array.";
            return false;
        }

        return true;
    }

    protected function _marshal($input): mixed
    {
        return json_encode($input ? JsonService::marshalItems($input, $this) : $input, $this->marshalFlags);
    }

    protected function _marshalDatabase($input): mixed
    {
        return $this->_marshal($input);
    }

    protected function _unmarshal($input): mixed
    {
        if (is_string($input)) {
            $data = @json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = $data;
            }
        }

        if (is_object($input)) {
            $input = (array)$input;
        }

        return is_array($input) ? JsonService::unmarshalItems($input, $this) : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $this->_unmarshal($input);
    }

    protected function _databaseType(): ?string
    {
        return (new Text([Text::nullable => $this->nullable]))->databaseType();
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'array';
    }
}