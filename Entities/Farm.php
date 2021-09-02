<?php

namespace Entities;

use Entities\Dataset\IranAdministrativeDivision;
use Entities\Farm\Field;
use Entities\Farm\FieldSignature;
use Entities\Farm\Media;
use Entities\Farm\Rate;
use Services\IranAdministrativeDivisionService;
use Services\Mime;
use Services\Size;
use Services\XUA\ExpressionService;
use Services\XUA\FileInstance;
use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Files\Generic;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
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
 * @property ?string story
 * @method static EntityFieldSignature F_story() The Signature of: Field `story`
 * @method static ConditionField C_story() The Condition Field of: Field `story`
 * @property Field[] additionalFields
 * @method static EntityFieldSignature F_additionalFields() The Signature of: Field `additionalFields`
 * @method static ConditionField C_additionalFields() The Condition Field of: Field `additionalFields`
 * @property int|float averageAnnualInterest
 * @method static EntityFieldSignature F_averageAnnualInterest() The Signature of: Field `averageAnnualInterest`
 * @method static ConditionField C_averageAnnualInterest() The Condition Field of: Field `averageAnnualInterest`
 * @property Field[] rates
 * @method static EntityFieldSignature F_rates() The Signature of: Field `rates`
 * @method static ConditionField C_rates() The Condition Field of: Field `rates`
 * @property float rate
 * @method static EntityFieldSignature F_rate() The Signature of: Field `rate`
 * @method static ConditionField C_rate() The Condition Field of: Field `rate`
 * @property string status
 * @method static EntityFieldSignature F_status() The Signature of: Field `status`
 * @method static ConditionField C_status() The Condition Field of: Field `status`
 * @property Media[] gallery
 * @method static EntityFieldSignature F_gallery() The Signature of: Field `gallery`
 * @method static ConditionField C_gallery() The Condition Field of: Field `gallery`
 * @property User owner
 * @method static EntityFieldSignature F_owner() The Signature of: Field `owner`
 * @method static ConditionField C_owner() The Condition Field of: Field `owner`
 * @property Product[] products
 * @method static EntityFieldSignature F_products() The Signature of: Field `products`
 * @method static ConditionField C_products() The Condition Field of: Field `products`
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
 * @property ?string address
 * @method static EntityFieldSignature F_address() The Signature of: Field `address`
 * @method static ConditionField C_address() The Condition Field of: Field `address`
 * @property null|int|float geolocationLat
 * @method static EntityFieldSignature F_geolocationLat() The Signature of: Field `geolocationLat`
 * @method static ConditionField C_geolocationLat() The Condition Field of: Field `geolocationLat`
 * @property null|int|float geolocationLong
 * @method static EntityFieldSignature F_geolocationLong() The Signature of: Field `geolocationLong`
 * @method static ConditionField C_geolocationLong() The Condition Field of: Field `geolocationLong`
 * @property ?string cooperationField
 * @method static EntityFieldSignature F_cooperationField() The Signature of: Field `cooperationField`
 * @method static ConditionField C_cooperationField() The Condition Field of: Field `cooperationField`
 * @property ?FileInstance proposal
 * @method static EntityFieldSignature F_proposal() The Signature of: Field `proposal`
 * @method static ConditionField C_proposal() The Condition Field of: Field `proposal`
 */
class Farm extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            # General Information
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
            'story' => new EntityFieldSignature(
                static::class, 'story',
                new Text(['nullable' => true, 'minLength' => 50, 'maxLength' => 1000]),
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
            'averageAnnualInterest' => new EntityFieldSignature(
                static::class, 'averageAnnualInterest',
                new Decimal(['nullable' => false, 'integerLength' => 3, 'fractionalLength' => 2, 'base' => 10]),
                null
            ),
            'rates' => new EntityFieldSignature(
                static::class, 'rates',
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
            'rate' => new EntityFieldSignature(
                static::class, 'rate',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): float {
                        $rates = array_map(function (Rate $rate) { return $rate->rate; }, $farm->rates);
                        return array_sum($rates) / count($rates);
                    }
                ]),
                null
            ),
            'status' => new EntityFieldSignature(
                static::class, 'status',
                new Enum(['nullable' => false, 'values' => ['approved', 'activated', 'deactivated']]),
                null
            ),
            'gallery' => new EntityFieldSignature(
                static::class, 'gallery',
                new EntityRelation([
                    'relatedEntity' => Media::class,
                    'relation' => 'IN',
                    'invName' => 'farm',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                []
            ),
            'owner' => new EntityFieldSignature(
                static::class, 'owner',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => 'NI',
                    'invName' => 'farms',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'products' => new EntityFieldSignature(
                static::class, 'products',
                new EntityRelation([
                    'relatedEntity' => Product::class,
                    'relation' => 'IN',
                    'invName' => 'farm',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
                ]),
                []
            ),
            # Farm Location Information
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
            'address' => new EntityFieldSignature(
                static::class, 'address',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 500]),
                null
            ),
            'geolocationLat' => new EntityFieldSignature(
                static::class, 'geolocationLat',
                new Decimal(['nullable' => true, 'integerLength' => 2, 'fractionalLength' => 10, 'base' => 10]),
                null
            ),
            'geolocationLong' => new EntityFieldSignature(
                static::class, 'geolocationLong',
                new Decimal(['nullable' => true, 'integerLength' => 2, 'fractionalLength' => 10, 'base' => 10]),
                null
            ),
            # Cooperation Information
            'cooperationField' => new EntityFieldSignature(
                static::class, 'cooperationField',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 100]),
                null
            ),
            'proposal' => new EntityFieldSignature(
                static::class, 'proposal',
                new Generic(['nullable' => true, 'allowedMimeTypes' => [Mime::MIME_APPLICATION_PDF], 'maxSize' => 10 * Size::MB]),
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
        if ($this->geographicDivision->type == 'ostan') {
            $exception->setError('geographicDivision', ExpressionService::get('errormessage.bad.geographic.division.with.title', [
                'title' => $this->geographicDivision->title,
            ]));
        }

        $thisAfsIds = [];
        foreach ($this->additionalFields as $af) {
            $thisAfsIds[$af->fieldSignature->id] = true;
        }

        $allAfses = FieldSignature::getMany();
        foreach ($allAfses as $afs) {
            if (!isset($thisAfsIds[$afs->id])) {
                $exception->setError('additionalFields', ExpressionService::get('errormessage.field.title.missing', ['title' => $afs->title]));
            }
        }

    }
}