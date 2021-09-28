<?php

namespace Entities\Farm;

use Entities\ChangeTracker;
use Entities\Farm;
use Services\SimpleTypeService;
use Supers\Basics\EntitySupers\EntityRelation;
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
 * @property \Entities\Farm\FieldSignature fieldSignature
 * @method static EntityFieldSignature F_fieldSignature() The Signature of: Field `fieldSignature`
 * @method static ConditionField C_fieldSignature() The Condition Field of: Field `fieldSignature`
 * @property ?string value
 * @method static EntityFieldSignature F_value() The Signature of: Field `value`
 * @method static ConditionField C_value() The Condition Field of: Field `value`
 * @property \Entities\Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
 */
class Field extends ChangeTracker
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'fieldSignature' => new EntityFieldSignature(
                static::class, 'fieldSignature',
                new EntityRelation([
                    'relatedEntity' => FieldSignature::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'value' => new EntityFieldSignature(
                static::class, 'value',
                new Text(['nullable' => true]),
                null
            ),
            'farm' => new EntityFieldSignature(
                static::class, 'farm',
                new EntityRelation([
                    'relatedEntity' => \Entities\Farm::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'additionalFields',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['farm' => Index::ASC, 'fieldSignature' => Index::ASC], true),
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if (! SimpleTypeService::validateValue($this->fieldSignature->type, $this->fieldSignature->typeParams, $this->value, $message)) {
            $exception->setError('value', $message);
        }
    }
}