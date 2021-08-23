<?php

namespace Entities;

use Entities\Dataset\IranAdministrativeDivision;
use Entities\Product\Category;
use Entities\Product\Field;
use Entities\Product\Media;
use Services\IranAdministrativeDivisionService;
use Services\Mime;
use Services\Size;
use Services\XUA\ExpressionService;
use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Files\Generic;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Numerics\DecimalRange;
use Supers\Basics\Strings\Text;
use Supers\Customs\Name;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property string description
 * @method static EntityFieldSignature F_description() The Signature of: Field `description`
 * @method static ConditionField C_description() The Condition Field of: Field `description`
 * @property \Entities\Product\Field[] additionalFields
 * @method static EntityFieldSignature F_additionalFields() The Signature of: Field `additionalFields`
 * @method static ConditionField C_additionalFields() The Condition Field of: Field `additionalFields`
 * @property Media[] gallery
 * @method static EntityFieldSignature F_gallery() The Signature of: Field `gallery`
 * @method static ConditionField C_gallery() The Condition Field of: Field `gallery`
 * @property Category category
 * @method static EntityFieldSignature F_category() The Signature of: Field `category`
 * @method static ConditionField C_category() The Condition Field of: Field `category`
 * @property IranAdministrativeDivision geographicDivision
 * @method static EntityFieldSignature F_geographicDivision() The Signature of: Field `geographicDivision`
 * @method static ConditionField C_geographicDivision() The Condition Field of: Field `geographicDivision`
 */
class Product extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'description' => new EntityFieldSignature(
                static::class, 'description',
                new Text(['nullable' => false, 'minLength' => 50, 'maxLength' => 1000]),
                null
            ),
            'additionalFields' => new EntityFieldSignature(
                static::class, 'additionalFields',
                new EntityRelation([
                    'relatedEntity' => Field::class,
                    'relation' => 'IN',
                    'invName' => 'product',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                []
            ),
            'investmentTimespan' => new EntityFieldSignature(
                static::class, 'investmentTimespan',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 100]),
                []
            ),
            'brochure' => new EntityFieldSignature(
                static::class, 'brochure',
                new Generic(['nullable' => true, 'allowedMimeTypes' => [Mime::MIME_APPLICATION_PDF], 'maxSize' => 10 * Size::MB]),
                null
            ),
            'price' => new EntityFieldSignature(
                static::class, 'price',
                new DecimalRange(['nullable' => false, 'fractionalLength' => 0, 'min' => 0, 'max' => 10_000_000_000]),
                null
            ),
            'gallery' => new EntityFieldSignature(
                static::class, 'gallery',
                new EntityRelation([
                    'relatedEntity' => Media::class,
                    'relation' => 'IN',
                    'invName' => 'product',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                []
            ),
            'category' => new EntityFieldSignature(
                static::class, 'category',
                new EntityRelation([
                    'relatedEntity' => Category::class,
                    'relation' => 'NI',
                    'invName' => 'products',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'costsTable' => new EntityFieldSignature(
                static::class, 'costsTable',
                new Sequence(['nullable' => false, 'type' => new Sequence(['nullable' => false, 'type' => new Text([])])]),
                null
            ),
            'predictionsTable' => new EntityFieldSignature(
                static::class, 'predictionsTable',
                new Sequence(['nullable' => false, 'type' => new Sequence(['nullable' => false, 'type' => new Text([])])]),
                null
            ),
            'geographicDivision' => new EntityFieldSignature(
                static::class, 'geographicDivision',
                new EntityRelation([
                    'relatedEntity' => IranAdministrativeDivision::class,
                    'relation' => 'NI',
                    'invName' => null,
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'ostan' => new EntityFieldSignature(
                static::class, 'ostan',
                new PhpVirtualField([
                    'getter' => function (Entity $entity) {
                        return IranAdministrativeDivisionService::getSpecificLevel($entity->geographicDivision, 'ostan');
                    }
                ]),
                null
            ),
            'shahrestan' => new EntityFieldSignature(
                static::class, 'shahrestan',
                new PhpVirtualField([
                    'getter' => function (Entity $entity) {
                        return IranAdministrativeDivisionService::getSpecificLevel($entity->geographicDivision, 'shahrestan');
                    }
                ]),
                null
            ),
            'bakhsh' => new EntityFieldSignature(
                static::class, 'bakhsh',
                new PhpVirtualField([
                    'getter' => function (Entity $entity) {
                        return IranAdministrativeDivisionService::getSpecificLevel($entity->geographicDivision, 'bakhsh');
                    }
                ]),
                null
            ),
            'dehestan' => new EntityFieldSignature(
                static::class, 'dehestan',
                new PhpVirtualField([
                    'getter' => function (Entity $entity) {
                        return IranAdministrativeDivisionService::getSpecificLevel($entity->geographicDivision, 'dehestan');
                    }
                ]),
                null
            ),
            'abadi' => new EntityFieldSignature(
                static::class, 'abadi',
                new PhpVirtualField([
                    'getter' => function (Entity $entity) {
                        return IranAdministrativeDivisionService::getSpecificLevel($entity->geographicDivision, 'abadi');
                    }
                ]),
                null
            ),
            'farm' => new EntityFieldSignature(
                static::class, 'farm',
                new EntityRelation([
                    'relatedEntity' => Farm::class,
                    'relation' => 'NI',
                    'invName' => 'products',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'paymentPlan' => new EntityFieldSignature(
                static::class, 'paymentPlan',
                new Sequence([
                    'nullable' => 'false',
                    'type' => new StructuredMap([
                        'structure' => [
                            'period' => new DecimalRange(['nullable' => false, 'fractionalLength' => 0, 'min' => 0, 'max' => 12]),
                            'amount' => new DecimalRange(['nullable' => false, 'fractionalLength' => 0, 'min' => 0, 'max' => 10_000_000_000]),
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

    protected function _validation(EntityFieldException $exception): void
    {
        $additionalFieldSignatureIds = [];
        foreach ($this->additionalFields as $additionalField) {
            $additionalFieldSignatureIds[$additionalField->fieldSignature->id] = true;
        }

        foreach ($this->category->additionalFields as $categoryAdditionalField) {
            if (!isset($additionalFieldSignatureIds[$categoryAdditionalField->id])) {
                $exception->setError('additionalFields', ExpressionService::get('errormessage.field.title.missing', ['title' => $categoryAdditionalField->title]));
            }
        }

        if ($this->geographicDivision->type == 'ostan') {
            $exception->setError('geographicDivision', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                'entity' => ExpressionService::get('entityclass.' . IranAdministrativeDivision::table()),
                'id' => $this->geographicDivision->id,
            ]));
        }
    }
}