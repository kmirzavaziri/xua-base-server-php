<?php

namespace XUA\CRUD;

use Services\XUA\Entity\EntityExtractService;
use XUA\Entity;
use XUA\Method;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

abstract class GetAllMethod extends Method
{
    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            static::resultName() => new MethodItemSignature(EntityExtractService::fieldsArrayType(static::entityFields()), true, null, false),
        ]);
    }

    protected function execute(): void
    {
        $this->{static::resultName()} = EntityExtractService::fieldsArray($this->all(), static::entityFields());
    }

    // Newly added methods

    /**
     * @return EntityFieldSignature[]
     */
    abstract protected static function entityFields(): array;

    /**
     * @return Entity[]
     */
    abstract protected function all(): array;

    abstract protected static function resultName(): string;
}