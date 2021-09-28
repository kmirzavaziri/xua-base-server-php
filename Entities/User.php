<?php

namespace Entities;

use Entities\User\Info\FarmOwner;
use Entities\User\Session;
use Services\Dataset\IranBankService;
use Services\Mime;
use Services\Size;
use Services\XUA\LocaleLanguage;
use Supers\Basics\Boolean;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
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
 * @property \Services\XUA\DateTimeInstance createdAt
 * @method static EntityFieldSignature F_createdAt() The Signature of: Field `createdAt`
 * @method static ConditionField C_createdAt() The Condition Field of: Field `createdAt`
 * @property \Entities\User createdBy
 * @method static EntityFieldSignature F_createdBy() The Signature of: Field `createdBy`
 * @method static ConditionField C_createdBy() The Condition Field of: Field `createdBy`
 * @property \Services\XUA\DateTimeInstance updatedAt
 * @method static EntityFieldSignature F_updatedAt() The Signature of: Field `updatedAt`
 * @method static ConditionField C_updatedAt() The Condition Field of: Field `updatedAt`
 * @property \Entities\User updatedBy
 * @method static EntityFieldSignature F_updatedBy() The Signature of: Field `updatedBy`
 * @method static ConditionField C_updatedBy() The Condition Field of: Field `updatedBy`
 * @property ?string firstNameFa
 * @method static EntityFieldSignature F_firstNameFa() The Signature of: Field `firstNameFa`
 * @method static ConditionField C_firstNameFa() The Condition Field of: Field `firstNameFa`
 * @property ?string firstNameEn
 * @method static EntityFieldSignature F_firstNameEn() The Signature of: Field `firstNameEn`
 * @method static ConditionField C_firstNameEn() The Condition Field of: Field `firstNameEn`
 * @property string titleFa
 * @method static EntityFieldSignature F_titleFa() The Signature of: Field `titleFa`
 * @method static ConditionField C_titleFa() The Condition Field of: Field `titleFa`
 * @property ?string lastNameFa
 * @method static EntityFieldSignature F_lastNameFa() The Signature of: Field `lastNameFa`
 * @method static ConditionField C_lastNameFa() The Condition Field of: Field `lastNameFa`
 * @property ?string lastNameEn
 * @method static EntityFieldSignature F_lastNameEn() The Signature of: Field `lastNameEn`
 * @method static ConditionField C_lastNameEn() The Condition Field of: Field `lastNameEn`
 * @property ?string bio
 * @method static EntityFieldSignature F_bio() The Signature of: Field `bio`
 * @method static ConditionField C_bio() The Condition Field of: Field `bio`
 * @property string titleEn
 * @method static EntityFieldSignature F_titleEn() The Signature of: Field `titleEn`
 * @method static ConditionField C_titleEn() The Condition Field of: Field `titleEn`
 * @property ?string nationalCode
 * @method static EntityFieldSignature F_nationalCode() The Signature of: Field `nationalCode`
 * @method static ConditionField C_nationalCode() The Condition Field of: Field `nationalCode`
 * @property ?string gender
 * @method static EntityFieldSignature F_gender() The Signature of: Field `gender`
 * @method static ConditionField C_gender() The Condition Field of: Field `gender`
 * @property null|\Services\XUA\DateTimeInstance birthDate
 * @method static EntityFieldSignature F_birthDate() The Signature of: Field `birthDate`
 * @method static ConditionField C_birthDate() The Condition Field of: Field `birthDate`
 * @property ?string nationality
 * @method static EntityFieldSignature F_nationality() The Signature of: Field `nationality`
 * @method static ConditionField C_nationality() The Condition Field of: Field `nationality`
 * @property ?string education
 * @method static EntityFieldSignature F_education() The Signature of: Field `education`
 * @method static ConditionField C_education() The Condition Field of: Field `education`
 * @property ?string job
 * @method static EntityFieldSignature F_job() The Signature of: Field `job`
 * @method static ConditionField C_job() The Condition Field of: Field `job`
 * @property ?\Services\XUA\FileInstance profilePicture
 * @method static EntityFieldSignature F_profilePicture() The Signature of: Field `profilePicture`
 * @method static ConditionField C_profilePicture() The Condition Field of: Field `profilePicture`
 * @property ?\Services\XUA\FileInstance nationalCardPicture
 * @method static EntityFieldSignature F_nationalCardPicture() The Signature of: Field `nationalCardPicture`
 * @method static ConditionField C_nationalCardPicture() The Condition Field of: Field `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance idBookletPicture
 * @method static EntityFieldSignature F_idBookletPicture() The Signature of: Field `idBookletPicture`
 * @method static ConditionField C_idBookletPicture() The Condition Field of: Field `idBookletPicture`
 * @property ?string organizationNameFa
 * @method static EntityFieldSignature F_organizationNameFa() The Signature of: Field `organizationNameFa`
 * @method static ConditionField C_organizationNameFa() The Condition Field of: Field `organizationNameFa`
 * @property ?string organizationNameEn
 * @method static EntityFieldSignature F_organizationNameEn() The Signature of: Field `organizationNameEn`
 * @method static ConditionField C_organizationNameEn() The Condition Field of: Field `organizationNameEn`
 * @property ?string organizationNationalId
 * @method static EntityFieldSignature F_organizationNationalId() The Signature of: Field `organizationNationalId`
 * @method static ConditionField C_organizationNationalId() The Condition Field of: Field `organizationNationalId`
 * @property ?string organizationRegistrationId
 * @method static EntityFieldSignature F_organizationRegistrationId() The Signature of: Field `organizationRegistrationId`
 * @method static ConditionField C_organizationRegistrationId() The Condition Field of: Field `organizationRegistrationId`
 * @property ?string cellphoneNumber
 * @method static EntityFieldSignature F_cellphoneNumber() The Signature of: Field `cellphoneNumber`
 * @method static ConditionField C_cellphoneNumber() The Condition Field of: Field `cellphoneNumber`
 * @property ?string email
 * @method static EntityFieldSignature F_email() The Signature of: Field `email`
 * @method static ConditionField C_email() The Condition Field of: Field `email`
 * @property ?string address
 * @method static EntityFieldSignature F_address() The Signature of: Field `address`
 * @method static ConditionField C_address() The Condition Field of: Field `address`
 * @property null|int|float geolocationLat
 * @method static EntityFieldSignature F_geolocationLat() The Signature of: Field `geolocationLat`
 * @method static ConditionField C_geolocationLat() The Condition Field of: Field `geolocationLat`
 * @property null|int|float geolocationLong
 * @method static EntityFieldSignature F_geolocationLong() The Signature of: Field `geolocationLong`
 * @method static ConditionField C_geolocationLong() The Condition Field of: Field `geolocationLong`
 * @property ?string postalCode
 * @method static EntityFieldSignature F_postalCode() The Signature of: Field `postalCode`
 * @method static ConditionField C_postalCode() The Condition Field of: Field `postalCode`
 * @property ?string landlinePhoneNumber
 * @method static EntityFieldSignature F_landlinePhoneNumber() The Signature of: Field `landlinePhoneNumber`
 * @method static ConditionField C_landlinePhoneNumber() The Condition Field of: Field `landlinePhoneNumber`
 * @property ?string faxNumber
 * @method static EntityFieldSignature F_faxNumber() The Signature of: Field `faxNumber`
 * @method static ConditionField C_faxNumber() The Condition Field of: Field `faxNumber`
 * @property ?string website
 * @method static EntityFieldSignature F_website() The Signature of: Field `website`
 * @method static ConditionField C_website() The Condition Field of: Field `website`
 * @property ?string personType
 * @method static EntityFieldSignature F_personType() The Signature of: Field `personType`
 * @method static ConditionField C_personType() The Condition Field of: Field `personType`
 * @property ?string iban
 * @method static EntityFieldSignature F_iban() The Signature of: Field `iban`
 * @method static ConditionField C_iban() The Condition Field of: Field `iban`
 * @property ?string bankAccountNo
 * @method static EntityFieldSignature F_bankAccountNo() The Signature of: Field `bankAccountNo`
 * @method static ConditionField C_bankAccountNo() The Condition Field of: Field `bankAccountNo`
 * @property ?string bankTitle
 * @method static EntityFieldSignature F_bankTitle() The Signature of: Field `bankTitle`
 * @method static ConditionField C_bankTitle() The Condition Field of: Field `bankTitle`
 * @property ?string referralMethod
 * @method static EntityFieldSignature F_referralMethod() The Signature of: Field `referralMethod`
 * @method static ConditionField C_referralMethod() The Condition Field of: Field `referralMethod`
 * @property ?string referralDetails
 * @method static EntityFieldSignature F_referralDetails() The Signature of: Field `referralDetails`
 * @method static ConditionField C_referralDetails() The Condition Field of: Field `referralDetails`
 * @property ?\Entities\User\Info\FarmOwner infoFarmOwner
 * @method static EntityFieldSignature F_infoFarmOwner() The Signature of: Field `infoFarmOwner`
 * @method static ConditionField C_infoFarmOwner() The Condition Field of: Field `infoFarmOwner`
 * @property bool verified
 * @method static EntityFieldSignature F_verified() The Signature of: Field `verified`
 * @method static ConditionField C_verified() The Condition Field of: Field `verified`
 * @property \Entities\User\Session[] sessions
 * @method static EntityFieldSignature F_sessions() The Signature of: Field `sessions`
 * @method static ConditionField C_sessions() The Condition Field of: Field `sessions`
 * @property bool admin
 * @method static EntityFieldSignature F_admin() The Signature of: Field `admin`
 * @method static ConditionField C_admin() The Condition Field of: Field `admin`
 * @property \Entities\Farm[] farms
 * @method static EntityFieldSignature F_farms() The Signature of: Field `farms`
 * @method static ConditionField C_farms() The Condition Field of: Field `farms`
 * @property \Entities\Farm\Rate[] farmRates
 * @method static EntityFieldSignature F_farmRates() The Signature of: Field `farmRates`
 * @method static ConditionField C_farmRates() The Condition Field of: Field `farmRates`
 * @property \Entities\Product\Rate[] productRates
 * @method static EntityFieldSignature F_productRates() The Signature of: Field `productRates`
 * @method static ConditionField C_productRates() The Condition Field of: Field `productRates`
 * @property \Entities\Order[] orders
 * @method static EntityFieldSignature F_orders() The Signature of: Field `orders`
 * @method static ConditionField C_orders() The Condition Field of: Field `orders`
 */
class User extends ChangeTracker
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_ = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    const NATIONALITY_IRANIAN = 'iranian';
    const NATIONALITY_FOREIGN = 'foreign';
    const NATIONALITY_ = [
        self::NATIONALITY_IRANIAN,
        self::NATIONALITY_FOREIGN,
    ];

    const PERSON_TYPE_JURIDICAL = 'juridical';
    const PERSON_TYPE_NATURAL = 'natural';
    const PERSON_TYPE_ = [
        self::PERSON_TYPE_JURIDICAL,
        self::PERSON_TYPE_NATURAL,
    ];

    const REFERRAL_METHOD_WEBSITE = 'website';
    const REFERRAL_METHOD_SOCIAL_MEDIA = 'social_media';
    const REFERRAL_METHOD_USER = 'user';
    const REFERRAL_METHOD_OTHER = 'other';
    const REFERRAL_METHOD_ = [
        self::REFERRAL_METHOD_WEBSITE,
        self::REFERRAL_METHOD_SOCIAL_MEDIA,
        self::REFERRAL_METHOD_USER,
        self::REFERRAL_METHOD_OTHER,
    ];

    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            # Natural Person / Juridical Person Agent Information
            'firstNameFa' => new EntityFieldSignature(
                static::class, 'firstNameFa',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'firstNameEn' => new EntityFieldSignature(
                static::class, 'firstNameEn',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_EN]),
                null
            ),
            'titleFa' => new EntityFieldSignature(
                static::class, 'titleFa',
                new PhpVirtualField([
                    'getter' => function (User $user): string {
                        return $user->firstNameFa . ' ' . $user->lastNameFa;
                    }
                ]),
                null
            ),
            'lastNameFa' => new EntityFieldSignature(
                static::class, 'lastNameFa',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'lastNameEn' => new EntityFieldSignature(
                static::class, 'lastNameEn',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_EN]),
                null
            ),
            'bio' => new EntityFieldSignature(
                static::class, 'bio',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 1000]),
                null
            ),
            'titleEn' => new EntityFieldSignature(
                static::class, 'titleEn',
                new PhpVirtualField([
                    'getter' => function (User $user): string {
                        return $user->firstNameEn . ' ' . $user->lastNameEn;
                    }
                ]),
                null
            ),
            'nationalCode' => new EntityFieldSignature(
                static::class, 'nationalCode',
                new IranNationalCode(['nullable' => true]),
                null
            ),
            'gender' => new EntityFieldSignature(
                static::class, 'gender',
                new Enum(['nullable' => true, 'values' => self::GENDER_]),
                null
            ),
            'birthDate' => new EntityFieldSignature(
                static::class, 'birthDate',
                new Date(['nullable' => true]),
                null
            ),
            'nationality' => new EntityFieldSignature(
                static::class, 'nationality',
                new Enum(['nullable' => true, 'values' => self::NATIONALITY_]),
                null
            ),
            'education' => new EntityFieldSignature(
                static::class, 'education',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'job' => new EntityFieldSignature(
                static::class, 'job',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'profilePicture' => new EntityFieldSignature(
                static::class, 'profilePicture',
                new Image([
                    'nullable' => true,
                    'unifier' => Mime::MIME_IMAGE_JPEG,
                    'maxSize' => 2 * Size::MB,
                    'maxWidth' => 1080,
                    'maxHeight' => 1080,
                    'ratioWidth' => 1,
                    'ratioHeight' => 1
                ]),
                null
            ),
            'nationalCardPicture' => new EntityFieldSignature(
                static::class, 'nationalCardPicture',
                new Image(['nullable' => true, 'unifier' => Mime::MIME_IMAGE_JPEG, 'maxSize' => 2 * Size::MB]),
                null
            ),
            'idBookletPicture' => new EntityFieldSignature(
                static::class, 'idBookletPicture',
                new Image(['nullable' => true, 'unifier' => Mime::MIME_IMAGE_JPEG, 'maxSize' => 2 * Size::MB]),
                null
            ),
            # Organization Information
            'organizationNameFa' => new EntityFieldSignature(
                static::class, 'organizationNameFa',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'organizationNameEn' => new EntityFieldSignature(
                static::class, 'organizationNameEn',
                new Name(['nullable' => true, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_EN]),
                null
            ),
            'organizationNationalId' => new EntityFieldSignature(
                static::class, 'organizationNationalId',
                new IranOrganizationNationalId(['nullable' => true]),
                null
            ),
            'organizationRegistrationId' => new EntityFieldSignature(
                static::class, 'organizationRegistrationId',
                new IranOrganizationRegistrationId(['nullable' => true]),
                null
            ),
            # Contact Information
            'cellphoneNumber' => new EntityFieldSignature(
                static::class, 'cellphoneNumber',
                new IranPhone(['nullable' => true, 'type' => IranPhone::TYPE_CELLPHONE]),
                null
            ),
            'email' => new EntityFieldSignature(
                static::class, 'email',
                new Email(['nullable' => true]),
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
            'postalCode' => new EntityFieldSignature(
                static::class, 'postalCode',
                new IranPostalCode(['nullable' => true]),
                null
            ),
            'landlinePhoneNumber' => new EntityFieldSignature(
                static::class, 'landlinePhoneNumber',
                new IranPhone(['nullable' => true, 'type' => IranPhone::TYPE_LANDLINE]),
                null
            ),
            'faxNumber' => new EntityFieldSignature(
                static::class, 'faxNumber',
                new IranPhone(['nullable' => true, 'type' => IranPhone::TYPE_LANDLINE]),
                null
            ),
            'website' => new EntityFieldSignature(
                static::class, 'website',
                new Url(['nullable' => true, 'schemes' => ['http://', 'https://']]),
                null
            ),
            # Other Information
            'personType' => new EntityFieldSignature(
                static::class, 'personType',
                new Enum(['nullable' => true, 'values' => self::PERSON_TYPE_]),
                null
            ),
            'iban' => new EntityFieldSignature(
                static::class, 'iban',
                new Iban(['nullable' => true]),
                null
            ),
            'bankAccountNo' => new EntityFieldSignature(
                static::class, 'bankAccountNo',
                new PhpVirtualField([
                    'getter' => function (User $user): ?string {
                        return IranBankService::getBankAccountNoFromIban($user->iban);
                    }
                ]),
                null
            ),
            'bankTitle' => new EntityFieldSignature(
                static::class, 'bankTitle',
                new PhpVirtualField([
                    'getter' => function (User $user): ?string {
                        return IranBankService::getBankFromIban($user->iban)[IranBankService::BANK_FIELD_TITLE];
                    }
                ]),
                null
            ),
            'referralMethod' => new EntityFieldSignature(
                static::class, 'referralMethod',
                new Enum(['nullable' => true, 'values' => self::REFERRAL_METHOD_]),
                null
            ),
            'referralDetails' => new EntityFieldSignature(
                static::class, 'referralDetails',
                new Text(['nullable' => true, 'minLength' => 1, 'maxLength' => 200]),
                null
            ),
            # Extra Information
            'infoFarmOwner' => new EntityFieldSignature(
                static::class, 'infoFarmOwner',
                new EntityRelation([
                    'relatedEntity' => FarmOwner::class,
                    'relation' => EntityRelation::REL_O11R,
                    'invName' => 'user',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            # Security Information
            'verified' => new EntityFieldSignature(
                static::class, 'verified',
                new Boolean([]),
                false
            ),
            'sessions' => new EntityFieldSignature(
                static::class, 'sessions',
                new EntityRelation([
                    'relatedEntity' => Session::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'user',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
            'admin' => new EntityFieldSignature(
                static::class, 'admin',
                new Boolean([]),
                false
            ),
            # Relational Information
            'farms' => new EntityFieldSignature(
                static::class, 'farms',
                new EntityRelation([
                    'relatedEntity' => \Entities\Farm::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'agent',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
            'farmRates' => new EntityFieldSignature(
                static::class, 'farmRates',
                new EntityRelation([
                    'relatedEntity' => \Entities\Farm\Rate::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'rater',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
            'productRates' => new EntityFieldSignature(
                static::class, 'productRates',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product\Rate::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'rater',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                []
            ),
            'orders' => new EntityFieldSignature(
                static::class, 'orders',
                new EntityRelation([
                    'relatedEntity' => \Entities\Order::class,
                    'relation' => EntityRelation::REL_1NR,
                    'invName' => 'customer',
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
}