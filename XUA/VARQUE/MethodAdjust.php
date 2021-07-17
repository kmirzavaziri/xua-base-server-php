<?php

namespace XUA\VARQUE;

use XUA\Entity;
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
            $request[$field->signature->name] = new MethodItemSignature($field->signature->type, $field->required, null, false);
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
            if ($this->{'Q_' . $field->signature->name} ?? false) {
                $feed->{$field->signature->name} = $this->{'Q_' . $field->signature->name};
            }
        }
        $feed->store();
    }

    # New Overridable Methods
    abstract protected static function entity(): string;

    /**
     * @return VarqueMethodFieldSignature[]
     */
    abstract protected static function fields(): array;

    abstract protected function feed(): Entity;
}