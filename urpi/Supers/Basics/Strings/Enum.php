<?php


namespace Supers\Basics\Strings;


use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
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

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if (!in_array($input, $this->values)) {
            $message = "Value '$input' is not a member of values (" . implode(', ', $this->values) . ").";
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