<?php

namespace Entities;


use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
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
 * @property \Entities\User[] workers
 * @method static EntityFieldSignature F_workers() The Signature of: Field `workers`
 * @method static ConditionField C_workers() The Condition Field of: Field `workers`
 */
class Farm extends Entity
{
    const id = 'id';
    const title = 'title';
    const workers = 'workers';

    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Text(['minLength' => 3, 'maxLength' => 200]),
                null
            ),
            'workers' => new EntityFieldSignature(
                static::class, 'workers',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => 'NN',
                    'invName' => 'workingFarms',
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
}