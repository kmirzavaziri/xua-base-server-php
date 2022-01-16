<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\LocaleLanguage;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

class Timezone extends Text
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::maxLength, false, 32,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
        ]);
    }

    protected function _predicate($input, array|string|null &$message = null): bool
    {
        $message = ExpressionService::getXua('supers.highers.timezone.error_message.invalid_timezone');

        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input)) {
            return false;
        }

        return in_array($input, LocaleLanguage::TZ_);
    }
}