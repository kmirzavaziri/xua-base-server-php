<?php

namespace Entities;


use Services\XUA\ExpressionService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Numerics\DecimalRange;
use Supers\Basics\Strings\Enum;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
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
 * @property \Entities\User customer
 * @method static EntityFieldSignature F_customer() The Signature of: Field `customer`
 * @method static ConditionField C_customer() The Condition Field of: Field `customer`
 * @property int amount
 * @method static EntityFieldSignature F_amount() The Signature of: Field `amount`
 * @method static ConditionField C_amount() The Condition Field of: Field `amount`
 * @property string status
 * @method static EntityFieldSignature F_status() The Signature of: Field `status`
 * @method static ConditionField C_status() The Condition Field of: Field `status`
 * @property \Entities\Item[] items
 * @method static EntityFieldSignature F_items() The Signature of: Field `items`
 * @method static ConditionField C_items() The Condition Field of: Field `items`
 * @property ?\Entities\Transaction transaction
 * @method static EntityFieldSignature F_transaction() The Signature of: Field `transaction`
 * @method static ConditionField C_transaction() The Condition Field of: Field `transaction`
 */
class Order extends ChangeTracker
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PAYING = 'paying';
    const STATUS_CANCELED = 'canceled';
    const STATUS_DONE = 'done';

    const STATUS_ = [
        self::STATUS_DRAFT,
        self::STATUS_PAYING,
        self::STATUS_CANCELED,
        self::STATUS_DONE,
    ];
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'customer' => new EntityFieldSignature(
                static::class, 'customer',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'orders',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'amount' => new EntityFieldSignature(
                static::class, 'amount',
                new DecimalRange(['nullable' => false, 'fractionalLength' => 0, 'min' => 0, 'max' => 10_000_000_000]),
                null
            ),
            'status' => new EntityFieldSignature(
                static::class, 'status',
                new Enum(['nullable' => false, 'values' => self::STATUS_]),
                null
            ),
            'items' => new EntityFieldSignature(
                static::class, 'items',
                new EntityRelation([
                    'relatedEntity' => Item::class,
                    'relation' => EntityRelation::REL_1NO,
                    'invName' => 'order',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'transaction' => new EntityFieldSignature(
                static::class, 'transaction',
                new EntityRelation([
                    'relatedEntity' => Transaction::class,
                    'relation' => EntityRelation::REL_O11O,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
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
        if ($this->status != self::STATUS_DRAFT and !$this->transaction) {
            $exception->setError('transaction', ExpressionService::get('errormessage.required.entity.field.not.provided'));
        }
    }
}