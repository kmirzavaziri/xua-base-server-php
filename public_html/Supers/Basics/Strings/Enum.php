<?php


namespace Supers\Basics\Strings;


use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
use XUA\Tools\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property array values
 */
class Enum extends Text
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, true),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, true),
            'values' => new SuperArgumentSignature(new Sequence(['type' => new Text([])]), true, null, false)
        ]);
    }

    protected function _validation(): void
    {
        if (count($this->values) < 1) {
            throw new SuperValidationException('List of values must contain at least one element.');
        }
        parent::_validation();
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