<?php


namespace XUA\Supers\Strings;


use XUA\Services\ExpressionService;
use XUA\Supers\Highers\Sequence;
use XUA\Supers\Numerics\Integer;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property array values
 * @method static SuperArgumentSignature A_values() The Signature of: Argument `values`
 */
class Enum extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, true),
            'values' => new SuperArgumentSignature(new Sequence(['type' => new Text([]), 'minLength' => 1]), true, null, false)
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
            $message = ExpressionService::get('errormessage.please.enter.a.valid.value.from.values', [
                'values' => implode(ExpressionService::get('comma.separator'), $this->values)
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