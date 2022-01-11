<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\DateTimeInstance;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\LocaleLanguage;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property string of
 * @property bool strict
 * @property bool acceptClass
 * @property bool nullable
 */
class Date extends Instance
{
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(true, static::of, false, DateTimeInstance::class,
                new Text([])
            ),
            Signature::new(true, static::strict, false, false,
                new Boolean([])
            ),
            Signature::new(true, static::acceptClass, false, false,
                new Boolean([])
            ),
        ]);
    }

    protected function _predicate($input, array|string|null &$message = null): bool
    {
        $message = ExpressionService::getXua('supers.highers.date.error_message.invalid_date', ['date' => is_string($input) ? $input : xua_var_dump($input)]);
        return parent::_predicate($input);
    }

    protected function _marshal($input): mixed
    {
        /** @var DateTimeInstance $input */
        return $input ? $input->format('Y-m-d') : $input;
    }

    protected function _unmarshal($input): mixed
    {
        return $input ? (DateTimeInstance::fromYmd($input) ?? $input) : $input;
    }

    protected function _marshalDatabase($input): mixed
    {
        /** @var ?DateTimeInstance $input */
        return $input ? $input->formatGregorian('Y-m-d', date_default_timezone_get(), 'en') : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $input ? (DateTimeInstance::fromGregorianYmd($input) ?? $input) : $input;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "DATE$nullExpression";
    }
}