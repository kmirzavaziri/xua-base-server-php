<?php

namespace Xua\Core\Supers\Highers;

use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Entity\RawSQL;
use Xua\Core\Tools\Signature\Signature;

class UUID extends Text
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(true, static::minLength, false, 36,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(true, static::maxLength, false, 36,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(true, static::nullable, false, true,
                new Boolean([])
            ),
        ]);
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        if ($input === null) {
            return new RawSQL('UUID()');
        }
        return parent::_marshalDatabase($input);
    }
}