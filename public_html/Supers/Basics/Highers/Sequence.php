<?php


namespace Supers\Basics\Highers;


use Supers\Basics\Numerics\Integer;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

/**
 * @property bool nullable
 * @property ?\XUA\Super type
 * @property ?int minLength
 * @property ?int maxLength
 */
class Sequence extends Json
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'type' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input)) {
            $message = gettype($input) . " is not array.";
            return false;
        }

        $length = count($input);
        $expectedKeys = range(0, $length - 1);
        $arrayKeys = array_keys($input);
        if ($input != [] and $arrayKeys !== $expectedKeys) {
            $expectedKeysString = $length > 3 ? "0, ..., " . ($length - 1) : implode(', ', $expectedKeys);
            $message = "Array keys (" . implode(', ', $arrayKeys) . ") are not the keys ($expectedKeysString).";
            return false;
        }

        if ($this->minLength !== null and $length < $this->minLength) {
            $message = "Length of input ($length) must be at least $this->minLength.";
            return false;
        }

        if ($this->maxLength !== null and $length > $this->maxLength) {
            $message = "Length of input ($length) must be at most $this->maxLength.";
            return false;
        }

        if ($this->type != null) {
            foreach ($input as $i => $item) {
                if (!$this->type->accepts($item)) {
                    $message = "Type $this->type does not accept sequence item number $i: " . xua_var_dump($item) . ".";
                    return false;
                }
            }
        }


        return true;
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'array';
//        return ($this->nullable ? '?' : '') . ($this->type->_phpType() != 'mixed' ? $this->type->_phpType() . '[]' : 'array');
    }
}