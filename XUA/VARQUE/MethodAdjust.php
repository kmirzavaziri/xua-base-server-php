<?php

namespace XUA\VARQUE;

use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\MethodEve;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

abstract class MethodAdjust extends MethodEve
{
    # Finalize Eve Methods
    final protected static function requestSignaturesCalculator(): array
    {
        $request = parent::requestSignaturesCalculator();
        $fields = static::fields();
        foreach ($fields as $field) {
            $request[$field->tree->value->name] = new MethodItemSignature($field->tree->type(), $field->required, $field->default, $field->const);
        }
        return $request;
    }

    final protected static function responseSignaturesCalculator(): array
    {
        return parent::responseSignaturesCalculator();
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fields();
        foreach ($fields as $field) {
            $feed->{$field->tree->value->name} = $field->tree->valueFromRequest($this->{'Q_' . $field->tree->value->name}, $field->tree->value->name);
        }
        try {
            $feed->store();
        } catch (EntityFieldException $e) {
            throw $this->error->fromException($e);
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