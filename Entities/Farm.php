<?php

namespace Entities;

use Supers\Basics\EntitySupers\EntityRelation;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 */
class Farm extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'products' => new EntityFieldSignature(
                static::class, 'products',
                new EntityRelation([
                    'relatedEntity' => Product::class,
                    'relation' => 'IN',
                    'invName' => 'farm',
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
        ]);
    }
}