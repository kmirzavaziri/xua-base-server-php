<?php

namespace Entities;

use Services\XUA\ExpressionService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\Product product
 * @method static EntityFieldSignature F_product() The Signature of: Field `product`
 * @method static ConditionField C_product() The Condition Field of: Field `product`
 * @property ?string code
 * @method static EntityFieldSignature F_code() The Signature of: Field `code`
 * @method static ConditionField C_code() The Condition Field of: Field `code`
 * @property string status
 * @method static EntityFieldSignature F_status() The Signature of: Field `status`
 * @method static ConditionField C_status() The Condition Field of: Field `status`
 * @property ?\Entities\Order order
 * @method static EntityFieldSignature F_order() The Signature of: Field `order`
 * @method static ConditionField C_order() The Condition Field of: Field `order`
 * @property \Entities\Report[] reports
 * @method static EntityFieldSignature F_reports() The Signature of: Field `reports`
 * @method static ConditionField C_reports() The Condition Field of: Field `reports`
 */
class Item extends Entity
{
    const STATUS_UNTRACKED = 'untracked';
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_WITHHELD = 'withheld';
    const STATUS_SOLD = 'sold';
    const STATUS_LOST = 'lost';
    const STATUS_ = [
        self::STATUS_UNTRACKED,
        self::STATUS_AVAILABLE,
        self::STATUS_RESERVED,
        self::STATUS_WITHHELD,
        self::STATUS_SOLD,
        self::STATUS_LOST,
    ];


    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'product' => new EntityFieldSignature(
                static::class, 'product',
                new EntityRelation([
                    'relatedEntity' => Product::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'items',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'code' => new EntityFieldSignature(
                static::class, 'code',
                new Text(['nullable' => true, 'minLength' => 20, 'maxLength' => 20]),
                null
            ),
            'status' => new EntityFieldSignature(
                static::class, 'status',
                new Enum(['nullable' => false, 'values' => self::STATUS_]),
                null
            ),
            'order' => new EntityFieldSignature(
                static::class, 'order',
                new EntityRelation([
                    'relatedEntity' => \Entities\Order::class,
                    'relation' => EntityRelation::REL_ON1,
                    'invName' => 'items',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                null
            ),
            'reports' => new EntityFieldSignature(
                static::class, 'reports',
                new EntityRelation([
                    'relatedEntity' => \Entities\Report::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'item',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if ($this->status != self::STATUS_UNTRACKED and !$this->code) {
            $exception->setError('code', ExpressionService::get('errormessage.required.entity.field.not.provided'));
        }
        if (!in_array($this->status, [self::STATUS_UNTRACKED, self::STATUS_AVAILABLE]) and !$this->order) {
            $exception->setError('order', ExpressionService::get('errormessage.required.entity.field.not.provided'));
        }
    }
}