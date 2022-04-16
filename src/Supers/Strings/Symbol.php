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
 * @property string unifier
 */
class Symbol extends Regex
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';
    const pattern = self::class . '::pattern';
    const allowEmpty = self::class . '::allowEmpty';
    const unifier = self::class . '::unifier';

    const UNIFIER_UPPER = 'upper';
    const UNIFIER_LOWER = 'lower';
    const UNIFIER_ = [
        self::UNIFIER_UPPER,
        self::UNIFIER_LOWER,
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::allowEmpty, false, false,
                new Boolean([])
            ),
            Signature::new(true, static::pattern, false, '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                new Text([])
            ),
            Signature::new(false, static::unifier, false, null,
                new Enum([
                    Enum::nullable => true,
                    Enum::values => self::UNIFIER_,
                ])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($input === '' and $this->allowEmpty) {
            return true;
        }
        return parent::_predicate($input, $message);
    }

    protected function _unmarshal($input): mixed
    {
        $input = parent::_unmarshal($input);
        return is_string($input) ? match ($this->unifier) {
            self::UNIFIER_UPPER => strtoupper($input),
            self::UNIFIER_LOWER => strtolower($input),
            default => $input
        } : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        $input = parent::_unmarshalDatabase($input);
        return match ($this->unifier) {
            self::UNIFIER_UPPER => strtoupper($input),
            self::UNIFIER_LOWER => strtolower($input),
            default => $input
        };
    }
}