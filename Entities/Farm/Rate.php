<?php

namespace Entities\Farm;

use Entities\Farm;
use Entities\User;
use Services\SimpleTypeService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Numerics\DecimalRange;
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
 * @property User rater
 * @method static EntityFieldSignature F_rater() The Signature of: Field `rater`
 * @method static ConditionField C_rater() The Condition Field of: Field `rater`
 * @property int rate
 * @method static EntityFieldSignature F_rate() The Signature of: Field `rate`
 * @method static ConditionField C_rate() The Condition Field of: Field `rate`
 * @property Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
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
                    'relation' => 'NI',
                    'invName' => 'ratedFarms',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                []
            ),
            'rate' => new EntityFieldSignature(
                static::class, 'rate',
                new DecimalRange(['nullable' => false, 'min' => 1, 'max' => 5, 'fractionalLength' => 0]),
                null
            ),
            'farm' => new EntityFieldSignature(
                static::class, 'farm',
                new EntityRelation([
                    'relatedEntity' => Farm::class,
                    'relation' => 'NI',
                    'invName' => 'rates',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
                ]),
                []
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['farm' => Index::ASC, 'rater' => Index::ASC], true),
        ]);
    }
}