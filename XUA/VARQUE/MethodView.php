<?php

namespace XUA\VARQUE;

use Exception;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

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
            if (is_a($field, EntityFieldSignature::class)) {
                $response[$field->name] = new MethodItemSignature($field->type, true, null, false);
            } elseif (is_a($field, EntityFieldSignatureTree::class)) {
                $response[$field->value->name] = new MethodItemSignature($field->type(), true, null, false);
            } else {
                throw new Exception('each field must be an instance of either EntityFieldSignature or EntityFieldSignatureTree');
            }
        }
        return $response;
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fields();
        foreach ($fields as $field) {
            if (is_a($field, EntityFieldSignature::class)) {
                $this->{$field->name} = $feed->{$field->name};
            } elseif (is_a($field, EntityFieldSignatureTree::class)) {
                $this->{$field->value->name} = $field->value($feed);
            } else {
                throw new Exception('each field must be an instance of either EntityFieldSignature or EntityFieldSignatureTree');
            }
        }
    }

    # New Overridable Methods
    abstract protected static function entity(): string;

    abstract protected static function fields(): array;

    abstract protected function feed(): Entity;
}