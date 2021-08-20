<?php

namespace XUA\VARQUE;

use XUA\Entity;
use XUA\MethodEve;
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
            $response[$field->tree->value->name] = new MethodItemSignature($field->tree->type(), true, null, false);
        }
        return $response;
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fields();
        foreach ($fields as $field) {
            $this->{$field->tree->value->name} = $field->tree->valueFromEntity($feed);
        }
    }

    # New Overridable Methods
    abstract protected static function entity(): string;

    /**
     * @return VarqueMethodFieldSignature[]
     */
    abstract protected static function fields(): array;

    abstract protected function feed(): Entity;
}