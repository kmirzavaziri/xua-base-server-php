<?php

namespace Entities\Product;

use Entities\Product;
use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Customs\Name;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property FieldSignature[] additionalFields
 * @method static EntityFieldSignature F_additionalFields() The Signature of: Field `additionalFields`
 * @method static ConditionField C_additionalFields() The Condition Field of: Field `additionalFields`
 * @property Product[] products
 * @method static EntityFieldSignature F_products() The Signature of: Field `products`
 * @method static ConditionField C_products() The Condition Field of: Field `products`
 */
class Category extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'additionalFields' => new EntityFieldSignature(
                static::class, 'additionalFields',
                new EntityRelation([
                    'relatedEntity' => FieldSignature::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'category',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'products' => new EntityFieldSignature(
                static::class, 'products',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'category',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
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