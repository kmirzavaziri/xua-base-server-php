<?php


namespace Supers\Basics\Highers;


use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property string of
 * @method static SuperArgumentSignature A_of() The Signature of: Argument `of`
 * @property bool strict
 * @method static SuperArgumentSignature A_strict() The Signature of: Argument `strict`
 * @property bool acceptClass
 * @method static SuperArgumentSignature A_acceptClass() The Signature of: Argument `acceptClass`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Instance extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
                'of' => new SuperArgumentSignature(new Text([]), true, null, false),
                'strict' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'acceptClass' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (is_object($input)) {
            if ($this->strict) {
                if (get_class($input) != $this->of) {
                    $message = "instance of class " . get_class($input) . " is not an instance of class $this->of.";
                    return false;
                }
            } else {
                if (!($input instanceof $this->of)) {
                    $message = "Class " . get_class($input) . " is not an subclass of class $this->of.";
                    return false;
                }
            }

            return true;
        } elseif(is_string($input) and $this->acceptClass) {
            if ($this->strict) {
                if ($input != $this->of) {
                    $message = "Class $input is not class $this->of.";
                    return false;
                }
            } else {
                if (!is_a($input, $this->of, true)) {
                    $message = "Class $input is not an subclass of class $this->of.";
                    return false;
                }
            }

            return true;
        } else {
            $message = xua_var_dump($input) . " is not a class or object.";
            return false;
        }
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? 'null|' : '') . "\\$this->of" . ($this->acceptClass ? '|string' : '');
    }
}