<?php

namespace Entities\Farm;

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
 * @property FieldSignature fieldSignature
 * @method static EntityFieldSignature F_fieldSignature() The Signature of: Field `fieldSignature`
 * @method static ConditionField C_fieldSignature() The Condition Field of: Field `fieldSignature`
 * @property ?string value
 * @method static EntityFieldSignature F_value() The Signature of: Field `value`
 * @method static ConditionField C_value() The Condition Field of: Field `value`
 * @property Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
 */
class Field extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'fieldSignature' => new EntityFieldSignature(
                static::class, 'fieldSignature',
                new EntityRelation([
                    'relatedEntity' => FieldSignature::class,
                    'relation' => 'NI',
                    'invName' => null,
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
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
                    'relatedEntity' => Farm::class,
                    'relation' => 'NI',
                    'invName' => 'additionalFields',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
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