<?php

namespace Entities\Product;

use Entities\Product;
use Services\SimpleTypeService;
use Services\XUA\ExpressionService;
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
 * @property Product product
 * @method static EntityFieldSignature F_product() The Signature of: Field `product`
 * @method static ConditionField C_product() The Condition Field of: Field `product`
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
            'product' => new EntityFieldSignature(
                static::class, 'product',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product::class,
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
            new Index(['product' => Index::ASC, 'fieldSignature' => Index::ASC], true),
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if ($this->product->category->id != $this->fieldSignature->category->id) {
            $exception->setError('fieldSignature', ExpressionService::get('errormessage.invalid.field.title.title', ['title' => $this->fieldSignature->title]));
            return;
        }
        if (! SimpleTypeService::validateValue($this->fieldSignature->type, $this->fieldSignature->typeParams, $this->value, $message)) {
            $exception->setError('value', $message);
        }
    }
}