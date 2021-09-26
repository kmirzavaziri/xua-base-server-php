<?php

namespace Entities;

use Entities\Product\Category;
use Entities\Product\Field;
use Entities\Product\Media;
use Entities\Product\Rate;
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
use XUA\Tools\Entity\Condition;
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
 * @property ?string investmentTimespan
 * @method static EntityFieldSignature F_investmentTimespan() The Signature of: Field `investmentTimespan`
 * @method static ConditionField C_investmentTimespan() The Condition Field of: Field `investmentTimespan`
 * @property \Entities\Product\Rate[] rates
 * @method static EntityFieldSignature F_rates() The Signature of: Field `rates`
 * @method static ConditionField C_rates() The Condition Field of: Field `rates`
 * @property float rate
 * @method static EntityFieldSignature F_rate() The Signature of: Field `rate`
 * @method static ConditionField C_rate() The Condition Field of: Field `rate`
 * @property ?\Services\XUA\FileInstance brochure
 * @method static EntityFieldSignature F_brochure() The Signature of: Field `brochure`
 * @method static ConditionField C_brochure() The Condition Field of: Field `brochure`
 * @property int price
 * @method static EntityFieldSignature F_price() The Signature of: Field `price`
 * @method static ConditionField C_price() The Condition Field of: Field `price`
 * @property \Entities\Product\Media[] gallery
 * @method static EntityFieldSignature F_gallery() The Signature of: Field `gallery`
 * @method static ConditionField C_gallery() The Condition Field of: Field `gallery`
 * @property ?string image
 * @method static EntityFieldSignature F_image() The Signature of: Field `image`
 * @method static ConditionField C_image() The Condition Field of: Field `image`
 * @property \Entities\Product\Category category
 * @method static EntityFieldSignature F_category() The Signature of: Field `category`
 * @method static ConditionField C_category() The Condition Field of: Field `category`
 * @property array costsTable
 * @method static EntityFieldSignature F_costsTable() The Signature of: Field `costsTable`
 * @method static ConditionField C_costsTable() The Condition Field of: Field `costsTable`
 * @property array predictionsTable
 * @method static EntityFieldSignature F_predictionsTable() The Signature of: Field `predictionsTable`
 * @method static ConditionField C_predictionsTable() The Condition Field of: Field `predictionsTable`
 * @property \Entities\Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
 * @property ?array paymentPlan
 * @method static EntityFieldSignature F_paymentPlan() The Signature of: Field `paymentPlan`
 * @method static ConditionField C_paymentPlan() The Condition Field of: Field `paymentPlan`
 * @property mixed stock
 * @method static EntityFieldSignature F_stock() The Signature of: Field `stock`
 * @method static ConditionField C_stock() The Condition Field of: Field `stock`
 * @property \Entities\Item[] items
 * @method static EntityFieldSignature F_items() The Signature of: Field `items`
 * @method static ConditionField C_items() The Condition Field of: Field `items`
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
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'product',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'investmentTimespan' => new EntityFieldSignature(
                static::class, 'investmentTimespan',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 100]),
                []
            ),
            'rates' => new EntityFieldSignature(
                static::class, 'rates',
                new EntityRelation([
                    'relatedEntity' => Rate::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'product',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'rate' => new EntityFieldSignature(
                static::class, 'rate',
                new PhpVirtualField([
                    'getter' => function (Product $product): float {
                        $rates = array_map(function (Rate $rate) { return $rate->rate; }, $product->rates);
                        return $rates ? (array_sum($rates) / count($rates)) : 0;
                    }
                ]),
                null
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
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'product',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
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
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'products',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
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
            'farm' => new EntityFieldSignature(
                static::class, 'farm',
                new EntityRelation([
                    'relatedEntity' => Farm::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'products',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
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
            'stock' => new EntityFieldSignature(
                static::class, 'stock',
                new PhpVirtualField(['getter' => function (Product $product) {
                    return Item::count(
                        Condition::leaf(Item::C_product()->rel(Product::C_id()), Condition::EQ, $product->id)
                            ->and(Item::C_status(), Condition::EQ, Item::STATUS_AVAILABLE)
                    );
                }]),
                0
            ),
            'items' => new EntityFieldSignature(
                static::class, 'items',
                new EntityRelation([
                    'relatedEntity' => \Entities\Item::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'product',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
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
        $thisAfsIds = [];
        foreach ($this->additionalFields as $additionalField) {
            $thisAfsIds[$additionalField->fieldSignature->id] = true;
        }

        $categoryAfses = $this->category->additionalFields;
        foreach ($categoryAfses as $categoryAfs) {
            if (!isset($thisAfsIds[$categoryAfs->id])) {
                $exception->setError('additionalFields', ExpressionService::get('errormessage.field.title.missing', ['title' => $categoryAfs->title]));
            }
        }
    }
}