<?php

namespace Entities;

use Services\Payment\PaymentService;
use Supers\Basics\Boolean;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\DateTime;
use Supers\Basics\Numerics\DecimalRange;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Entity\Index;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\User user
 * @method static EntityFieldSignature F_user() The Signature of: Field `user`
 * @method static ConditionField C_user() The Condition Field of: Field `user`
 * @property string paymentService
 * @method static EntityFieldSignature F_paymentService() The Signature of: Field `paymentService`
 * @method static ConditionField C_paymentService() The Condition Field of: Field `paymentService`
 * @property string paymentServiceUid
 * @method static EntityFieldSignature F_paymentServiceUid() The Signature of: Field `paymentServiceUid`
 * @method static ConditionField C_paymentServiceUid() The Condition Field of: Field `paymentServiceUid`
 * @property int amount
 * @method static EntityFieldSignature F_amount() The Signature of: Field `amount`
 * @method static ConditionField C_amount() The Condition Field of: Field `amount`
 * @property ?string transactionReference
 * @method static EntityFieldSignature F_transactionReference() The Signature of: Field `transactionReference`
 * @method static ConditionField C_transactionReference() The Condition Field of: Field `transactionReference`
 * @property bool verified
 * @method static EntityFieldSignature F_verified() The Signature of: Field `verified`
 * @method static ConditionField C_verified() The Condition Field of: Field `verified`
 * @property \Services\XUA\DateTimeInstance createdAt
 * @method static EntityFieldSignature F_createdAt() The Signature of: Field `createdAt`
 * @method static ConditionField C_createdAt() The Condition Field of: Field `createdAt`
 * @property \Services\XUA\DateTimeInstance verifiedAt
 * @method static EntityFieldSignature F_verifiedAt() The Signature of: Field `verifiedAt`
 * @method static ConditionField C_verifiedAt() The Condition Field of: Field `verifiedAt`
 */
class Transaction extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'user' => new EntityFieldSignature(
                static::class, 'user',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => 'NI',
                    'invName' => null,
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'paymentService' => new EntityFieldSignature(
                static::class, 'paymentService',
                new Enum(['nullable' => false, 'values' => PaymentService::SERVICE_ALL]),
                null
            ),
            'paymentServiceUid' => new EntityFieldSignature(
                static::class, 'paymentServiceUid',
                new Text(['nullable' => false, 'maxLength' => 36]),
                null
            ),
            'amount' => new EntityFieldSignature(
                static::class, 'amount',
                new DecimalRange(['nullable' => false, 'fractionalLength' => 0, 'min' => 0, 'max' => 10_000_000_000]),
                null
            ),
            'transactionReference' => new EntityFieldSignature(
                static::class, 'transactionReference',
                new Text(['nullable' => true, 'maxLength' => 36]), // @TODO
                null
            ),
            'verified' => new EntityFieldSignature(
                static::class, 'verified',
                new Boolean([]),
                null
            ),
            'createdAt' => new EntityFieldSignature(
                static::class, 'createdAt',
                new DateTime(['nullable' => false]),
                null
            ),
            'verifiedAt' => new EntityFieldSignature(
                static::class, 'verifiedAt',
                new DateTime(['nullable' => true]),
                null
            ),
       ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['paymentService' => Index::ASC, 'paymentServiceUid' => Index::ASC], true),
        ]);
    }
}