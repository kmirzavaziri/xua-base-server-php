<?php


namespace Supers\Basics\Highers;



use Services\XUA\JsonService;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Json extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ]);
    }

    protected function _predicate($input, string &$message = null): bool
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
        return json_encode($input ? JsonService::marshalItems($input, $this) : $input);
    }

    protected function _marshalDatabase($input): mixed
    {
        return $this->_marshal($input);
    }

    protected function _unmarshal($input): mixed
    {
        $modified = false;
        if (is_string($input)) {
            $data = @json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = $data;
                $modified = true;
            }
        } elseif (is_object($input)) {
            $input = (array)$input;
            $modified = true;
        }

        if ($modified) {
            return JsonService::unmarshalItems($input, $this);
        }
        return $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $this->_unmarshal($input);
    }

    protected function _databaseType(): ?string
    {
        return (new Text(['nullable' => $this->nullable]))->databaseType();
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'array';
    }
}