<?php

namespace Xua\Core\Supers\Strings;

use Xua\Core\Supers\Boolean;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property string pattern
 * @property bool allowEmpty
 */
class Symbol extends Regex
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';
    const pattern = self::class . '::pattern';
    const allowEmpty = self::class . '::allowEmpty';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::allowEmpty, false, false,
                new Boolean([])
            ),
            Signature::new(true, static::pattern, false, '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                new Text([])
            )
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($input === '' and $this->allowEmpty) {
            return true;
        }
        return parent::_predicate($input, $message);
    }
}