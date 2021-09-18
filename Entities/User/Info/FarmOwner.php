<?php

namespace Entities\User\Info;

use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Strings\Text;
use Supers\Customs\IranPhone;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\User user
 * @method static EntityFieldSignature F_user() The Signature of: Field `user`
 * @method static ConditionField C_user() The Condition Field of: Field `user`
 * @property ?string bankAccountType
 * @method static EntityFieldSignature F_bankAccountType() The Signature of: Field `bankAccountType`
 * @method static ConditionField C_bankAccountType() The Condition Field of: Field `bankAccountType`
 * @property string emergencyPhoneNumber
 * @method static EntityFieldSignature F_emergencyPhoneNumber() The Signature of: Field `emergencyPhoneNumber`
 * @method static ConditionField C_emergencyPhoneNumber() The Condition Field of: Field `emergencyPhoneNumber`
 * @property ?string skills
 * @method static EntityFieldSignature F_skills() The Signature of: Field `skills`
 * @method static ConditionField C_skills() The Condition Field of: Field `skills`
 * @property ?string experiences
 * @method static EntityFieldSignature F_experiences() The Signature of: Field `experiences`
 * @method static ConditionField C_experiences() The Condition Field of: Field `experiences`
 */
class FarmOwner extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'user' => new EntityFieldSignature(
                static::class, 'user',
                new EntityRelation([
                    'relatedEntity' => \Entities\User::class,
                    'relation' => EntityRelation::REL_R11O,
                    'invName' => 'infoFarmOwner',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
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
                static::class, 'skills',
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