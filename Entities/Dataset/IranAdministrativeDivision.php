<?php

namespace Entities\Dataset;

use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Customs\Name;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Entity\Index;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property string type
 * @method static EntityFieldSignature F_type() The Signature of: Field `type`
 * @method static ConditionField C_type() The Condition Field of: Field `type`
 * @property IranAdministrativeDivision[] children
 * @method static EntityFieldSignature F_children() The Signature of: Field `children`
 * @method static ConditionField C_children() The Condition Field of: Field `children`
 * @property ?IranAdministrativeDivision parent
 * @method static EntityFieldSignature F_parent() The Signature of: Field `parent`
 * @method static ConditionField C_parent() The Condition Field of: Field `parent`
 */
class IranAdministrativeDivision extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'id' => new EntityFieldSignature(
                static::class, 'id',
                new Decimal(['nullable' => false, 'integerLength' => 6, 'fractionalLength' => 0, 'base' => 10]),
                null
            ),
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'type' => new EntityFieldSignature(
                static::class, 'type',
                new Enum(['nullable' => false, 'values' => ['ostan', 'shahrestan', 'bakhsh', 'dehestan', 'shahrOrRoosta']]),
                null
            ),
            'children' => new EntityFieldSignature(
                static::class, 'children',
                new EntityRelation([
                    'relatedEntity' => IranAdministrativeDivision::class,
                    'relation' => EntityRelation::REL_1NO,
                    'invName' => 'parent',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'parent' => new EntityFieldSignature(
                static::class, 'parent',
                new EntityRelation([
                    'relatedEntity' => IranAdministrativeDivision::class,
                    'relation' => EntityRelation::REL_ON1,
                    'invName' => 'children',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
            new Index(['type' => Index::ASC], false),
        ]);
    }
}