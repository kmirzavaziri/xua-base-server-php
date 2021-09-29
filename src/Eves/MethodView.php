<?php

namespace XUA\Eves;

use XUA\Exceptions\MagicCallException;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

abstract class MethodView extends MethodEve
{
    # Finalize Eve Methods
    final protected static function requestSignaturesCalculator(): array
    {
        return parent::requestSignaturesCalculator();
    }

    final protected static function responseSignaturesCalculator(): array
    {
        $response = parent::responseSignaturesCalculator();
        $fields = static::fields();
        foreach ($fields as $field) {
            $type = $field->root->type();
            try {
                @$type->nullable = true;
            } catch (MagicCallException $e) {
                // It's OK
            }
            $response[$field->root->name()] = new MethodItemSignature($type, true, null, false);
        }
        return $response;
    }

    protected function body(): void
    {
        $feed = $this->feed();
        if (!$feed) {
            return;
        }
        $fields = static::fields();
        foreach ($fields as $field) {
            $this->{$field->root->name()} = $field->root->valueFromEntity($feed);
        }
    }

    # New Overridable Methods
    protected static function entity(): string
    {
        return Entity::class;
    }

    /**
     * @return VarqueMethodFieldSignature[]
     */
    protected static function fields(): array
    {
        return [];
    }

    abstract protected function feed(): Entity;
}