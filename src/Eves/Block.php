<?php

namespace Xua\Core\Eves;

use Xua\Core\Exceptions\MagicCallException;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Tools\Signature\Signature;

abstract class Block extends Xua
{
    /**
     * @var mixed[][]
     */
    protected array $_x_values = [];

    final public static function signature(string $halfName) : Signature
    {
        return Signature::_(static::class, $halfName);
    }

    final public function __get(string $halfName)
    {
        $signature = Signature::_(static::class, $halfName);

        if ($signature === null) {
            throw (new MagicCallException())->setError($halfName, ExpressionService::get('xua.eves.block.error_message.unknown_signature_name', ['signatureName' => static::class . '::' . $halfName]));
        }

        $value = &$this->_x_values[$signature->prefix][$signature->name];
        $this->getterProcedure($signature->prefix, $signature->name, $signature, $value);

        return $value;
    }

    final public function __set(string $halfName, mixed $value): void
    {
        $signature = Signature::_(static::class, $halfName);

        if ($signature === null) {
            throw (new MagicCallException())->setError($halfName, ExpressionService::get('xua.eves.block.error_message.unknown_signature_name', ['signatureName' => static::class . '::' . $halfName]));
        }

        $this->setterProcedure($signature->prefix, $signature->name, $signature, $value);

        $this->_x_values[$signature->prefix][$signature->name] = $value;
    }

    protected static function _init(): void
    {
        static::registerSignatures();
    }

    protected function getterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void {}

    protected function setterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void {}

    protected static function registerSignatures(): void {}
}