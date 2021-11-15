<?php

namespace Xua\Core\Supers\Strings;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property array values
 */
class Enum extends Text
{
    const minLength = self::class . '::minLength';
    const maxLength = self::class . '::maxLength';
    const nullable = self::class . '::nullable';
    const values = self::class . '::values';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(true, static::minLength, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(true, static::maxLength, false, null,
                new Integer([Integer::unsigned => true, Integer::nullable => true])
            ),
            Signature::new(false, static::values, true, null,
                new Sequence([Sequence::type => new Text([]), Sequence::minLength => 1])
            )
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if (!in_array($input, $this->values)) {
            $message = ExpressionService::get('xua.supers.strings.enum.error_message.please_enter_a_valid_value_from_values', [
                'values' => ExpressionService::implode($this->values, ExpressionService::IMPLODE_MODE_DISJUNCTION)
            ]);
            return false;
        }

        return true;
    }

    protected function _databaseType(): ?string
    {
        if (!$this->values) {
            return null;
        }

        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "ENUM('" . implode("','", $this->values) . "')$nullExpression";
    }
}