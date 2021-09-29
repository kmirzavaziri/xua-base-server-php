<?php


namespace XUA\Supers\Highers;


use XUA\Supers\Numerics\Integer;
use XUA\Exceptions\SuperValidationException;
use XUA\Eves\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property null|\XUA\Eves\Super type
 * @method static SuperArgumentSignature A_type() The Signature of: Argument `type`
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 */
class Sequence extends Json
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'type' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'minLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'maxLength' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if ($this->minLength !== null and $this->maxLength !== null and $this->minLength > $this->maxLength) {
            $exception->setError('maxLength', "Max length $this->maxLength cannot be less than min length $this->minLength");
        }
    }

    protected function _predicate($input, null|string|array &$message = null): bool
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

        $message = [];
        if ($this->type != null) {
            foreach ($input as $i => $item) {
                if (!$this->type->_predicate($item, $itemMessage)) {
                    $message[$i] = $itemMessage;
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