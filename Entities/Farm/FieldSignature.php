<?php

namespace Entities\Farm;

use Entities\ChangeTracker;
use Services\SimpleTypeService;
use Supers\Basics\Highers\Map;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Entity\Index;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Services\XUA\DateTimeInstance createdAt
 * @method static EntityFieldSignature F_createdAt() The Signature of: Field `createdAt`
 * @method static ConditionField C_createdAt() The Condition Field of: Field `createdAt`
 * @property \Entities\User createdBy
 * @method static EntityFieldSignature F_createdBy() The Signature of: Field `createdBy`
 * @method static ConditionField C_createdBy() The Condition Field of: Field `createdBy`
 * @property \Services\XUA\DateTimeInstance updatedAt
 * @method static EntityFieldSignature F_updatedAt() The Signature of: Field `updatedAt`
 * @method static ConditionField C_updatedAt() The Condition Field of: Field `updatedAt`
 * @property \Entities\User updatedBy
 * @method static EntityFieldSignature F_updatedBy() The Signature of: Field `updatedBy`
 * @method static ConditionField C_updatedBy() The Condition Field of: Field `updatedBy`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property ?string type
 * @method static EntityFieldSignature F_type() The Signature of: Field `type`
 * @method static ConditionField C_type() The Condition Field of: Field `type`
 * @property ?array typeParams
 * @method static EntityFieldSignature F_typeParams() The Signature of: Field `typeParams`
 * @method static ConditionField C_typeParams() The Condition Field of: Field `typeParams`
 */
class FieldSignature extends ChangeTracker
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Text(['nullable' => false, 'minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'type' => new EntityFieldSignature(
                static::class, 'type',
                new Enum(['nullable' => true, 'values' => SimpleTypeService::TYPE_]),
                null
            ),
            'typeParams' => new EntityFieldSignature(
                static::class, 'typeParams',
                new Map(['nullable' => true]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['title' => Index::ASC], true),
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if (!SimpleTypeService::validateTypeParams($this->type, $this->typeParams, $message)) {
            $exception->setError('typeParams', $message);
        }
    }
}