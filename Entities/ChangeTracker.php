<?php

namespace Entities;

use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\DateTime;
use XUA\Entity;
use XUA\Tools\Signature\EntityFieldSignature;

abstract class ChangeTracker extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'createdAt' => new EntityFieldSignature(
                static::class, 'createdAt',
                new DateTime(['nullable' => false]),
                null
            ),
            'createdBy' => new EntityFieldSignature(
                static::class, 'createdBy',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'updatedAt' => new EntityFieldSignature(
                static::class, 'updatedAt',
                new DateTime(['nullable' => false]),
                null
            ),
            'updatedBy' => new EntityFieldSignature(
                static::class, 'updatedBy',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
       ]);
    }
}