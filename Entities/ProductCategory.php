<?php

namespace Entities;

use Entities\User\Session;
use Services\Mime;
use Services\Size;
use Services\XUA\LocaleLanguage;
use Supers\Basics\Boolean;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Files\Image;
use Supers\Basics\Highers\Date;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use Supers\Customs\Email;
use Supers\Customs\Iban;
use Supers\Customs\IranNationalCode;
use Supers\Customs\IranOrganizationNationalId;
use Supers\Customs\IranOrganizationRegistrationId;
use Supers\Customs\IranPhone;
use Supers\Customs\IranPostalCode;
use Supers\Customs\Name;
use Supers\Customs\Url;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string titleFa
 * @method static EntityFieldSignature F_titleFa() The Signature of: Field `titleFa`
 * @method static ConditionField C_titleFa() The Condition Field of: Field `titleFa`
 * @property string titleEn
 * @method static EntityFieldSignature F_titleEn() The Signature of: Field `titleEn`
 * @method static ConditionField C_titleEn() The Condition Field of: Field `titleEn`
 */
class ProductCategory extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'titleFa' => new EntityFieldSignature(
                static::class, 'titleFa',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'titleEn' => new EntityFieldSignature(
                static::class, 'titleEn',
                new Name(['nullable' => false, 'minLength' => 0, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_EN]),
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