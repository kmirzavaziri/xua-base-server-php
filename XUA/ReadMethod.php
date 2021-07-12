<?php

namespace XUA;

use Services\XUA\Entity\EntityExtractService;
use XUA\Tools\Signature\MethodItemSignature;

abstract class ReadMethod extends Method
{
    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            static::resultName() => new MethodItemSignature(EntityExtractService::fieldsArrayType(static::entityFields()), true, null, false),
        ]);
    }

    protected function execute(): void
    {
        $this->{static::resultName()} = EntityExtractService::fieldsArray($this->entityItems(), static::entityFields());
    }

    // Newly added methods
    abstract protected static function entityFields(): array;

    abstract protected function entityItems(): array;

    abstract protected static function resultName(): string;
}