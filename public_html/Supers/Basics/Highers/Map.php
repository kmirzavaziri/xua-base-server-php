<?php


namespace Supers\Basics\Highers;


use Supers\Basics\Numerics\Integer;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @property ?\XUA\Super keyType
 * @property ?\XUA\Super valueType
 * @property ?int minSize
 * @property ?int maxSize
 */
class Map extends Json
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'keyType' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'valueType' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'minSize' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'maxSize' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_array($input) and !is_object($input)) {
            $message = gettype($input) . " is neither array nor object.";
            return false;
        }

        if (is_object($input)) {
            $input = (array)$input;
        }

        $size = count($input);

        if ($this->minSize !== null and $size < $this->minSize) {
            $message = "Size of input ($size) must be at least $this->minSize.";
            return false;
        }

        if ($this->maxSize !== null and $size > $this->maxSize) {
            $message = "Size of input ($size) must be at most $this->maxSize.";
            return false;
        }

        if ($this->keyType != null) {
            foreach ($input as $key => $value) {
                if(!$this->keyType->accepts($key)) {
                    $message = "Type $this->keyType does not accept map key $key.";
                    return false;
                }
            }
        }
        if ($this->valueType != null) {
            foreach ($input as $key => $value) {
                if(!$this->valueType->accepts($value)) {
                    $message = "Type $this->valueType does not accept map item $key: " . xua_var_dump($value) . ".";
                    return false;
                }
            }
        }

        return true;
    }
}