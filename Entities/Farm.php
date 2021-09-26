<?php

namespace Entities;

use Entities\Dataset\IranAdministrativeDivision;
use Entities\Farm\Field;
use Entities\Farm\FieldSignature;
use Entities\Farm\Media;
use Entities\Farm\Rate;
use Services\Dataset\IranAdministrativeDivisionService;
use Services\Mime;
use Services\Size;
use Services\XUA\ExpressionService;
use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Files\Generic;
use Supers\Basics\Files\Image;
use Supers\Basics\Highers\Date;
use Supers\Basics\Highers\StructuredMap;
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
 * @property \Entities\Farm\Field[] additionalFields
 * @method static EntityFieldSignature F_additionalFields() The Signature of: Field `additionalFields`
 * @method static ConditionField C_additionalFields() The Condition Field of: Field `additionalFields`
 * @property int|float averageAnnualInterest
 * @method static EntityFieldSignature F_averageAnnualInterest() The Signature of: Field `averageAnnualInterest`
 * @method static ConditionField C_averageAnnualInterest() The Condition Field of: Field `averageAnnualInterest`
 * @property \Entities\Farm\Rate[] rates
 * @method static EntityFieldSignature F_rates() The Signature of: Field `rates`
 * @method static ConditionField C_rates() The Condition Field of: Field `rates`
 * @property float rate
 * @method static EntityFieldSignature F_rate() The Signature of: Field `rate`
 * @method static ConditionField C_rate() The Condition Field of: Field `rate`
 * @property string status
 * @method static EntityFieldSignature F_status() The Signature of: Field `status`
 * @method static ConditionField C_status() The Condition Field of: Field `status`
 * @property \Entities\Farm\Media[] gallery
 * @method static EntityFieldSignature F_gallery() The Signature of: Field `gallery`
 * @method static ConditionField C_gallery() The Condition Field of: Field `gallery`
 * @property ?string image
 * @method static EntityFieldSignature F_image() The Signature of: Field `image`
 * @method static ConditionField C_image() The Condition Field of: Field `image`
 * @property \Entities\User agent
 * @method static EntityFieldSignature F_agent() The Signature of: Field `agent`
 * @method static ConditionField C_agent() The Condition Field of: Field `agent`
 * @property string agentType
 * @method static EntityFieldSignature F_agentType() The Signature of: Field `agentType`
 * @method static ConditionField C_agentType() The Condition Field of: Field `agentType`
 * @property \Entities\Product[] products
 * @method static EntityFieldSignature F_products() The Signature of: Field `products`
 * @method static ConditionField C_products() The Condition Field of: Field `products`
 * @property string ownership
 * @method static EntityFieldSignature F_ownership() The Signature of: Field `ownership`
 * @method static ConditionField C_ownership() The Condition Field of: Field `ownership`
 * @property ?\Services\XUA\FileInstance agreementPicture
 * @method static EntityFieldSignature F_agreementPicture() The Signature of: Field `agreementPicture`
 * @method static ConditionField C_agreementPicture() The Condition Field of: Field `agreementPicture`
 * @property array deedDetails
 * @method static EntityFieldSignature F_deedDetails() The Signature of: Field `deedDetails`
 * @method static ConditionField C_deedDetails() The Condition Field of: Field `deedDetails`
 * @property \Entities\Dataset\IranAdministrativeDivision geographicDivision
 * @method static EntityFieldSignature F_geographicDivision() The Signature of: Field `geographicDivision`
 * @method static ConditionField C_geographicDivision() The Condition Field of: Field `geographicDivision`
 * @property \Entities\Dataset\IranAdministrativeDivision ostan
 * @method static EntityFieldSignature F_ostan() The Signature of: Field `ostan`
 * @method static ConditionField C_ostan() The Condition Field of: Field `ostan`
 * @property \Entities\Dataset\IranAdministrativeDivision shahrestan
 * @method static EntityFieldSignature F_shahrestan() The Signature of: Field `shahrestan`
 * @method static ConditionField C_shahrestan() The Condition Field of: Field `shahrestan`
 * @property ?\Entities\Dataset\IranAdministrativeDivision bakhsh
 * @method static EntityFieldSignature F_bakhsh() The Signature of: Field `bakhsh`
 * @method static ConditionField C_bakhsh() The Condition Field of: Field `bakhsh`
 * @property ?\Entities\Dataset\IranAdministrativeDivision dehestan
 * @method static EntityFieldSignature F_dehestan() The Signature of: Field `dehestan`
 * @method static ConditionField C_dehestan() The Condition Field of: Field `dehestan`
 * @property ?\Entities\Dataset\IranAdministrativeDivision shahrOrRoosta
 * @method static EntityFieldSignature F_shahrOrRoosta() The Signature of: Field `shahrOrRoosta`
 * @method static ConditionField C_shahrOrRoosta() The Condition Field of: Field `shahrOrRoosta`
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
 * @property ?\Services\XUA\FileInstance proposal
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
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'farm',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
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
                    'relatedEntity' => Rate::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'farm',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'rate' => new EntityFieldSignature(
                static::class, 'rate',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): float {
                        $rates = array_map(function (Rate $rate) { return $rate->rate; }, $farm->rates);
                        return $rates ? (array_sum($rates) / count($rates)) : 0;
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
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'farm',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                []
            ),
            'image' => new EntityFieldSignature(
                static::class, 'image',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): ?string {
                        return $farm->gallery ? (Media::F_source()->type)->marshal($farm->gallery[0]->source) : null;
                    }
                ]),
                null
            ),
            'agent' => new EntityFieldSignature(
                static::class, 'agent',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'farms',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'agentType' => new EntityFieldSignature(
                static::class, 'agentType' .
                '',
                new Enum(['nullable' => false, 'values' => ['owner', 'attorney']]),
                null
            ),
            'products' => new EntityFieldSignature(
                static::class, 'products',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'farm',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
            'ownership' => new EntityFieldSignature(
                static::class, 'ownership' .
                '',
                new Enum(['nullable' => false, 'values' => ['deed', 'agreement']]),
                null
            ),
            'agreementPicture' => new EntityFieldSignature(
                static::class, 'agreementPicture',
                new Image(['nullable' => true, 'unifier' => Mime::MIME_IMAGE_JPEG, 'maxSize' => 2 * Size::MB]),
                null
            ),
            'deedDetails' => new EntityFieldSignature(
                static::class, 'deedDetails',
                new StructuredMap([
                    'nullable' => true,
                    'structure' => [
                        'propertyNumber' => new Text(['maxLength' => 20]),
                        'registrationNumber' => new Text(['maxLength' => 20]),
                        'registrationDate' => new Date([]),
                        'volume' => new Text(['maxLength' => 5]),
                        'page' => new Text(['maxLength' => 5]),
                    ]
                ]),
                null
            ),
            # Farm Location Information
            'geographicDivision' => new EntityFieldSignature(
                static::class, 'geographicDivision',
                new EntityRelation([
                    'relatedEntity' => IranAdministrativeDivision::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'ostan' => new EntityFieldSignature(
                static::class, 'ostan',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($farm->geographicDivision, 'ostan');
                    }
                ]),
                null
            ),
            'shahrestan' => new EntityFieldSignature(
                static::class, 'shahrestan',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($farm->geographicDivision, 'shahrestan');
                    }
                ]),
                null
            ),
            'bakhsh' => new EntityFieldSignature(
                static::class, 'bakhsh',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($farm->geographicDivision, 'bakhsh');
                    }
                ]),
                null
            ),
            'dehestan' => new EntityFieldSignature(
                static::class, 'dehestan',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($farm->geographicDivision, 'dehestan');
                    }
                ]),
                null
            ),
            'shahrOrRoosta' => new EntityFieldSignature(
                static::class, 'shahrOrRoosta',
                new PhpVirtualField([
                    'getter' => function (Farm $farm): ?IranAdministrativeDivision {
                        return IranAdministrativeDivisionService::getSpecificLevel($farm->geographicDivision, 'shahrOrRoosta');
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
        // Geographic Division
        if ($this->geographicDivision->type == 'ostan') {
            $exception->setError('geographicDivision', ExpressionService::get('errormessage.bad.geographic.division.with.title', [
                'title' => $this->geographicDivision->title,
            ]));
        }

        // Additional Fields
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

        // Deed & Agreement
        if ($this->ownership == 'agreement') {
            if (!$this->agreementPicture) {
                $exception->setError('agreementPicture', ExpressionService::get('errormessage.required.entity.field.not.provided'));
            }
            if ($this->deedDetails) {
                $exception->setError('deedDetails', ExpressionService::get('errormessage.entity.field.must.be.empty'));
            }
        }
        if ($this->ownership == 'deed') {
            if (!$this->deedDetails) {
                $exception->setError('deedDetails', ExpressionService::get('errormessage.required.entity.field.not.provided'));
            }
            if ($this->agreementPicture) {
                $exception->setError('agreementPicture', ExpressionService::get('errormessage.entity.field.must.be.empty'));
            }
        }
    }
}