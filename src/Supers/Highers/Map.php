<?php


namespace XUA\Supers\Highers;


use XUA\Supers\Numerics\Integer;
use XUA\Eves\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property null|\XUA\Eves\Super keyType
 * @method static SuperArgumentSignature A_keyType() The Signature of: Argument `keyType`
 * @property null|\XUA\Eves\Super valueType
 * @method static SuperArgumentSignature A_valueType() The Signature of: Argument `valueType`
 * @property ?int minSize
 * @method static SuperArgumentSignature A_minSize() The Signature of: Argument `minSize`
 * @property ?int maxSize
 * @method static SuperArgumentSignature A_maxSize() The Signature of: Argument `maxSize`
 */
class Map extends Json
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'keyType' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'valueType' => new SuperArgumentSignature(new Instance(['of' => Super::class, 'nullable' => true]), false, null, false),
            'minSize' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
            'maxSize' => new SuperArgumentSignature(new Integer(['unsigned' => true, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
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