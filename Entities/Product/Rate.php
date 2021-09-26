<?php

namespace Entities\Product;

use Entities\Product;
use Entities\User;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Numerics\DecimalRange;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Entity\Index;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\User rater
 * @method static EntityFieldSignature F_rater() The Signature of: Field `rater`
 * @method static ConditionField C_rater() The Condition Field of: Field `rater`
 * @property int rate
 * @method static EntityFieldSignature F_rate() The Signature of: Field `rate`
 * @method static ConditionField C_rate() The Condition Field of: Field `rate`
 * @property \Entities\Product product
 * @method static EntityFieldSignature F_product() The Signature of: Field `product`
 * @method static ConditionField C_product() The Condition Field of: Field `product`
 */
class Rate extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'rater' => new EntityFieldSignature(
                static::class, 'rater',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'productRates',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'rate' => new EntityFieldSignature(
                static::class, 'rate',
                new DecimalRange(['nullable' => false, 'min' => 1, 'max' => 5, 'fractionalLength' => 0]),
                null
            ),
            'product' => new EntityFieldSignature(
                static::class, 'product',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'rates',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['product' => Index::ASC, 'rater' => Index::ASC], true),
        ]);
    }
}