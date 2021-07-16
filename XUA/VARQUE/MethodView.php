<?php

namespace XUA\VARQUE;

use Services\XUA\Entity\EntityAsArrayService;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodView extends MethodEve
{
    final protected static function requestSignaturesCalculator(): array
    {
        return static::_requestSignatures();
    }

    final protected static function responseSignaturesCalculator(): array
    {
        return array_merge(parent::responseSignaturesCalculator(), [
            static::fieldsWrapper() => new MethodItemSignature(EntityAsArrayService::fieldsType(static::fields()), true, null, false),
        ]);
    }

    final protected function execute(): void
    {
        $this->{static::fieldsWrapper()} = EntityAsArrayService::getFields($this->feed(), static::fields());
    }

    abstract protected static function entity(): string;

    /**
     * @return EntityFieldSignature[]
     */
    abstract protected static function fields(): array;

    abstract protected function feed(): Entity;

    protected static function fieldsWrapper(): string
    {
        return lcfirst(static::entity());
    }
}