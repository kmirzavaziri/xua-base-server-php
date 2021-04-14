<?php


namespace Supers\Basics\Highers;


use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

class Instance extends Super
{
    protected static function _arguments(): array
    {
        return [
                'of' => new SuperArgumentSignature(new Text([]), true, null, false),
                'strict' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ] + parent::_arguments();
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_object($input)) {
            $message = gettype($input) . " is not object.";
            return false;
        }

        if ($this->strict) {
            if (get_class($input) != $this->of) {
                $message = "instance of class " . get_class($input) . " is not an instance of $this->of.";
                return false;
            }
        } else {
            if (!($input instanceof $this->of)) {
                $message = "Class " . get_class($input) . " is not an subclass of $this->of.";
                return false;
            }
        }


        return true;
    }

}