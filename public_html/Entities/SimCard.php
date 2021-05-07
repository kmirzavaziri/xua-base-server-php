<?php

namespace Entities;


use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Tools\EntityFieldSignature;
use XUA\Tools\Index;

/**
 * @property int id
 * @property User owner
 * @property User[] lastOwners
 * @property string code
 */
class SimCard extends Entity
{
    protected static function _fields(): array
    {
        return array_merge(parent::_fields(), [
            'owner' => new EntityFieldSignature(
                static::class, 'owner',
                new EntityRelation([
                    'relation' => 'II',
                    'relatedEntity' => User::class,
                    'invName' => 'simCard',
                    'nullable' => false,
                ]),
                null
            ),
            'lastOwners' => new EntityFieldSignature(
                static::class, 'lastOwner',
                new EntityRelation([
                    'relation' => 'IN',
                    'relatedEntity' => User::class,
                    'invName' => 'lastSimCard',
                    'invNullable' => true
                ]),
                []
            ),
            'code' => new EntityFieldSignature(
                static::class, 'code',
                new Text(['minLength' => 10, 'maxLength' => 30]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['code' => 'ASC'], true)
        ]);
    }
}