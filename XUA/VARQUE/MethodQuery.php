<?php

namespace XUA\VARQUE;

use Services\XUA\Entity\EntityAsArrayService;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodQuery extends MethodEve
{
    final protected static function requestSignaturesCalculator(): array
    {
        return parent::requestSignaturesCalculator();
    }

    final protected static function responseSignaturesCalculator(): array
    {
        return array_merge(parent::responseSignaturesCalculator(), [
            static::fieldsWrapper() => new MethodItemSignature(EntityAsArrayService::fieldsArrayType(static::fields()), true, null, false),
        ]);
    }

    final protected function execute(): void
    {
        $this->{static::fieldsWrapper()} = EntityAsArrayService::getFieldsArray($this->feed(), static::fields());
    }

    abstract protected static function entity(): string;

    /**
     * @return EntityFieldSignature[]
     */
    abstract protected static function fields(): array;

    /**
     * @return Entity[]
     */
    abstract protected function feed(): array;

    protected static function fieldsWrapper(): string
    {
        return lcfirst(static::entity()) . 's';
    }
}