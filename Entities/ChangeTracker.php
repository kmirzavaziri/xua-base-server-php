<?php

namespace Entities;

use Services\UserService;
use Services\XUA\DateTimeInstance;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\DateTime;
use XUA\Entity;
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
 */
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

    protected function _store(string $caller): Entity
    {
        $now = new DateTimeInstance();
        if (!$this->createdBy) {
            $this->createdBy = UserService::user();
        }
        if (!$this->createdAt) {
            $this->createdAt = $now;
        }
        $this->updatedBy = UserService::user();
        $this->updatedAt = $now;
        return parent::_store($caller);
    }
}