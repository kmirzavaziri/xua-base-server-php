<?php

namespace Xua\Core\Supers\Strings;

use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property string pattern
 */
class Regex extends Text
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';
    const pattern = self::class . '::pattern';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::pattern, true, null,
                new Text([])
            )
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        parent::_validation($exception);
        if (@preg_match($this->pattern, '') === FALSE) {
            $exception->setError('pattern', 'Provided pattern is not a valid regex.');
        }
    }

    protected function _predicate($input, null|string|array &$message = null): bool
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