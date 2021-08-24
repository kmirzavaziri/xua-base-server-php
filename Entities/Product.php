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
use Services\XUA\FileInstance;
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
 * @property Field[] additionalFields
 * @method static EntityFieldSignature F_additionalFields() The Signature of: Field `additionalFields`
 * @method static ConditionField C_additionalFields() The Condition Field of: Field `additionalFields`
 * @property ?string investmentTimespan
 * @method static EntityFieldSignature F_investmentTimespan() The Signature of: Field `investmentTimespan`
 * @method static ConditionField C_investmentTimespan() The Condition Field of: Field `investmentTimespan`
 * @property ?FileInstance brochure
 * @method static EntityFieldSignature F_brochure() The Signature of: Field `brochure`
 * @method static ConditionField C_brochure() The Condition Field of: Field `brochure`
 * @property int price
 * @method static EntityFieldSignature F_price() The Signature of: Field `price`
 * @method static ConditionField C_price() The Condition Field of: Field `price`
 * @property Media[] gallery
 * @method static EntityFieldSignature F_gallery() The Signature of: Field `gallery`
 * @method static ConditionField C_gallery() The Condition Field of: Field `gallery`
 * @property ?string image
 * @method static EntityFieldSignature F_image() The Signature of: Field `image`
 * @method static ConditionField C_image() The Condition Field of: Field `image`
 * @property Category category
 * @method static EntityFieldSignature F_category() The Signature of: Field `category`
 * @method static ConditionField C_category() The Condition Field of: Field `category`
 * @property array costsTable
 * @method static EntityFieldSignature F_costsTable() The Signature of: Field `costsTable`
 * @method static ConditionField C_costsTable() The Condition Field of: Field `costsTable`
 * @property array predictionsTable
 * @method static EntityFieldSignature F_predictionsTable() The Signature of: Field `predictionsTable`
 * @method static ConditionField C_predictionsTable() The Condition Field of: Field `predictionsTable`
 * @property IranAdministrativeDivision geographicDivision
 * @method static EntityFieldSignature F_geographicDivision() The Signature of: Field `geographicDivision`
 * @method static ConditionField C_geographicDivision() The Condition Field of: Field `geographicDivision`
 * @property IranAdministrativeDivision ostan
 * @method static EntityFieldSignature F_ostan() The Signature of: Field `ostan`
 * @method static ConditionField C_ostan() The Condition Field of: Field `ostan`
 * @property IranAdministrativeDivision shahrestan
 * @method static EntityFieldSignature F_shahrestan() The Signature of: Field `shahrestan`
 * @method static ConditionField C_shahrestan() The Condition Field of: Field `shahrestan`
 * @property ?IranAdministrativeDivision bakhsh
 * @method static EntityFieldSignature F_bakhsh() The Signature of: Field `bakhsh`
 * @method static ConditionField C_bakhsh() The Condition Field of: Field `bakhsh`
 * @property ?IranAdministrativeDivision dehestan
 * @method static EntityFieldSignature F_dehestan() The Signature of: Field `dehestan`
 * @method static ConditionField C_dehestan() The Condition Field of: Field `dehestan`
 * @property ?IranAdministrativeDivision abadi
 * @method static EntityFieldSignature F_abadi() The Signature of: Field `abadi`
 * @method static ConditionField C_abadi() The Condition Field of: Field `abadi`
 * @property Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
 * @property ?array paymentPlan
 * @method static EntityFieldSignature F_paymentPlan() The Signature of: Field `paymentPlan`
 * @method static ConditionField C_paymentPlan() The Condition Field of: Field `paymentPlan`
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
            'image' => new EntityFieldSignature(
                static::class, 'image',
                new PhpVirtualField([
                    'getter' => function (Product $product): ?string {
                        return $product->gallery ? (Media::F_source()->type)->marshal($product->gallery[0]->source) : null;
                    }
                ]),
                null
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
                    'getter' => function (Product $product): IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($product->geographicDivision, 'ostan');
                    }
                ]),
                null
            ),
            'shahrestan' => new EntityFieldSignature(
                static::class, 'shahrestan',
                new PhpVirtualField([
                    'getter' => function (Product $product): IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($product->geographicDivision, 'shahrestan');
                    }
                ]),
                null
            ),
            'bakhsh' => new EntityFieldSignature(
                static::class, 'bakhsh',
                new PhpVirtualField([
                    'getter' => function (Product $product): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($product->geographicDivision, 'bakhsh');
                    }
                ]),
                null
            ),
            'dehestan' => new EntityFieldSignature(
                static::class, 'dehestan',
                new PhpVirtualField([
                    'getter' => function (Product $product): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($product->geographicDivision, 'dehestan');
                    }
                ]),
                null
            ),
            'abadi' => new EntityFieldSignature(
                static::class, 'abadi',
                new PhpVirtualField([
                    'getter' => function (Product $product): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($product->geographicDivision, 'abadi');
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
            $exception->setError('geographicDivision', ExpressionService::get('errormessage.bad.geographic.division.with.title', [
                'title' => $this->geographicDivision->title,
            ]));
        }
    }
}