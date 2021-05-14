<?php


namespace Supers\Basics\Highers;



use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 */
class Json extends Super
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input) and !is_object($input)) {
            $message = gettype($input) . " is neither array nor object.";
            return false;
        }

        return true;
    }

    protected function _marshal($input): mixed
    {
        return json_encode($input);
    }

    protected function _marshalDatabase($input): mixed
    {
        return $this->_marshal($input);
    }

    protected function _unmarshal($input): mixed
    {
        if (is_string($input)) {
            $data = json_decode($input);
            if ($data !== null) {
                return $data;
            }
        }

        return $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $this->_unmarshal($input);
    }

    protected function _databaseType(): ?string
    {
        return (new Text([]))->databaseType();
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? 'null|' : '') . 'array|object';
    }
}