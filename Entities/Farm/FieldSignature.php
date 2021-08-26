<?php

namespace Entities\Farm;

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
class FieldSignature extends Entity
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
                new Enum([
                    'nullable' => true,
                    'values' => SimpleTypeService::TYPES
                ]),
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