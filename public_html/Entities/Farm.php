<?php

namespace Entities;


use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Tools\EntityFieldSignature;

/**
 * @property int id
 * @property string title
 * @property User[] workers
 */
class Farm extends Entity
{
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