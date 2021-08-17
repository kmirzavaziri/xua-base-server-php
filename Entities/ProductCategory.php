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
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Symbol;
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
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property array detailsFields
 * @method static EntityFieldSignature F_detailsFields() The Signature of: Field `detailsFields`
 * @method static ConditionField C_detailsFields() The Condition Field of: Field `detailsFields`
 */
class ProductCategory extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'detailsFields' => new EntityFieldSignature(
                static::class, 'title',
                new Sequence([
                    'type' => new StructuredMap([
                        'structure' => [
                            'key' => new Symbol(['nullable' => false, 'minLength' => 1, 'maxLength' => 200]),
                            'title' => new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                        ]
                    ])
                ]),
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