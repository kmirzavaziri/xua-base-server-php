<?php


namespace Supers\Basics\Strings;


use Supers\Basics\Boolean;
use Supers\Basics\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property string pattern
 */
class Regex extends Text
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'pattern' => new SuperArgumentSignature(new Text([]), true, null, false)
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        parent::_validation($exception);
        if (@preg_match($this->pattern, '') === FALSE) {
            $exception->setError('pattern', 'Provided pattern is not a valid regex.');
        }
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if (!preg_match($this->pattern, $input)) {
            $message = "Pattern '$this->pattern' does not match the value '$input'";
            return false;
        }

        return true;
    }
}