<?php

namespace Entities;


use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @property string title
 * @property \Entities\User[] workers
 */
class Farm extends Entity
{
    const id = 'id';
    const title = 'title';
    const workers = 'workers';

    protected static function _fields(): array
    {
        return array_merge(parent::_fields(), [
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