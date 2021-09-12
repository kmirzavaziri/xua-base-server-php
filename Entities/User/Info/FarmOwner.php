<?php

namespace Entities\User\Info;

use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
use Supers\Customs\IranPhone;
use XUA\Entity;
use XUA\Tools\Signature\EntityFieldSignature;

class FarmOwner extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'user' => new EntityFieldSignature(
                static::class, 'user',
                new EntityRelation([
                    'relatedEntity' => \Entities\User::class,
                    'relation' => 'II',
                    'invName' => 'infoFarmOwner',
                    'nullable' => false,
                    'invNullable' => true,
                    'definedOn' => 'there',
                ]),
                null
            ),
            'bankAccountType' => new EntityFieldSignature(
                static::class, 'bankAccountType',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 50]),
                null
            ),
            'emergencyPhoneNumber' => new EntityFieldSignature(
                static::class, 'emergencyPhoneNumber',
                new IranPhone(['nullable' => false, 'type' => 'both']),
                null
            ),
            'skills' => new EntityFieldSignature(
                static::class, 'skill',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 1000]),
                null
            ),
            'experiences' => new EntityFieldSignature(
                static::class, 'experiences',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 1000]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }
}