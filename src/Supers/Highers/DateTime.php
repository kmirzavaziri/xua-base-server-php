<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\DateTimeInstance;
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
class DateTime extends Instance
{
    const of = self::class . '::of';
    const strict = self::class . '::strict';
    const acceptClass = self::class . '::acceptClass';
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

    protected function _marshal($input): mixed
    {
        /** @var DateTimeInstance $input */
        return $input ? $input->format('Y-m-d H:i:s') : $input;
    }

    protected function _unmarshal($input): mixed
    {
        return $input ? (DateTimeInstance::fromYmdHis($input) ?? $input) : $input;
    }

    protected function _marshalDatabase($input): mixed
    {
        /** @var ?DateTimeInstance $input */
        return $input ? $input->formatGregorian('Y-m-d H:i:s', date_default_timezone_get()) : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $input ? (DateTimeInstance::fromGregorianYmdHis($input) ?? $input) : $input;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "DATETIME$nullExpression";
    }
}