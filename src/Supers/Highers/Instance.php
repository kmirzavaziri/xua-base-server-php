<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property string of
 * @property bool strict
 * @property bool acceptClass
 * @property bool acceptObject
 * @property bool nullable
 */
class Instance extends Super
{
    const of = self::class . '::of';
    const strict = self::class . '::strict';
    const acceptClass = self::class . '::acceptClass';
    const acceptObject = self::class . '::acceptObject';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::of, true, null,
                new Text([])
            ),
            Signature::new(false, static::strict, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::acceptClass, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::acceptObject, false, true,
                new Boolean([])
            ),
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
            ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (is_object($input) and $this->acceptObject) {
            if ($this->strict) {
                if (get_class($input) != $this->of) {
                    $message = "instance of class " . get_class($input) . " is not an instance of class $this->of."; // @TODO message from dict
                    return false;
                }
            } else {
                if (!($input instanceof $this->of)) {
                    $message = "Class " . get_class($input) . " is not a subclass of class $this->of."; // @TODO message from dict
                    return false;
                }
            }

            return true;
        } elseif(is_string($input) and $this->acceptClass) {
            if ($this->strict) {
                if ($input != $this->of) {
                    $message = "Class $input is not class $this->of."; // @TODO message from dict
                    return false;
                }
            } else {
                if (!is_a($input, $this->of, true)) {
                    $message = "Class $input is not a subclass of class $this->of."; // @TODO message from dict
                    return false;
                }
            }

            return true;
        } else {
            $message = xua_var_dump($input) . " is not a class or object."; // @TODO message from dict
            return false;
        }
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? 'null|' : '') . "\\$this->of" . ($this->acceptClass ? '|string' : '');
    }
}