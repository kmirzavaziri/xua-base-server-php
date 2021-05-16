<?php

namespace Entities;

use Entities\User\Session;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Date;
use Supers\Basics\Highers\File;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use Supers\Customs\Email;
use Supers\Customs\Iban;
use Supers\Customs\IranNationalCode;
use Supers\Customs\IranNationalId;
use Supers\Customs\IranPhone;
use Supers\Customs\IranPostalCode;
use Supers\Customs\Url;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string personType
 * @method static EntityFieldSignature F_personType() The Signature of: Field `personType`
 * @method static ConditionField C_personType() The Condition Field of: Field `personType`
 * @property string firstNameFa
 * @method static EntityFieldSignature F_firstNameFa() The Signature of: Field `firstNameFa`
 * @method static ConditionField C_firstNameFa() The Condition Field of: Field `firstNameFa`
 * @property string firstNameEn
 * @method static EntityFieldSignature F_firstNameEn() The Signature of: Field `firstNameEn`
 * @method static ConditionField C_firstNameEn() The Condition Field of: Field `firstNameEn`
 * @property string lastNameFa
 * @method static EntityFieldSignature F_lastNameFa() The Signature of: Field `lastNameFa`
 * @method static ConditionField C_lastNameFa() The Condition Field of: Field `lastNameFa`
 * @property string lastNameEn
 * @method static EntityFieldSignature F_lastNameEn() The Signature of: Field `lastNameEn`
 * @method static ConditionField C_lastNameEn() The Condition Field of: Field `lastNameEn`
 * @property string nationalCode
 * @method static EntityFieldSignature F_nationalCode() The Signature of: Field `nationalCode`
 * @method static ConditionField C_nationalCode() The Condition Field of: Field `nationalCode`
 * @property ?string gender
 * @method static EntityFieldSignature F_gender() The Signature of: Field `gender`
 * @method static ConditionField C_gender() The Condition Field of: Field `gender`
 * @property mixed birthDate
 * @method static EntityFieldSignature F_birthDate() The Signature of: Field `birthDate`
 * @method static ConditionField C_birthDate() The Condition Field of: Field `birthDate`
 * @property ?string nationality
 * @method static EntityFieldSignature F_nationality() The Signature of: Field `nationality`
 * @method static ConditionField C_nationality() The Condition Field of: Field `nationality`
 * @property string education
 * @method static EntityFieldSignature F_education() The Signature of: Field `education`
 * @method static ConditionField C_education() The Condition Field of: Field `education`
 * @property string job
 * @method static EntityFieldSignature F_job() The Signature of: Field `job`
 * @method static ConditionField C_job() The Condition Field of: Field `job`
 * @property mixed profilePicture
 * @method static EntityFieldSignature F_profilePicture() The Signature of: Field `profilePicture`
 * @method static ConditionField C_profilePicture() The Condition Field of: Field `profilePicture`
 * @property mixed nationalCardPicture
 * @method static EntityFieldSignature F_nationalCardPicture() The Signature of: Field `nationalCardPicture`
 * @method static ConditionField C_nationalCardPicture() The Condition Field of: Field `nationalCardPicture`
 * @property mixed birthCertificatePicture
 * @method static EntityFieldSignature F_birthCertificatePicture() The Signature of: Field `birthCertificatePicture`
 * @method static ConditionField C_birthCertificatePicture() The Condition Field of: Field `birthCertificatePicture`
 * @property ?string organizationName
 * @method static EntityFieldSignature F_organizationName() The Signature of: Field `organizationName`
 * @method static ConditionField C_organizationName() The Condition Field of: Field `organizationName`
 * @property ?string organizationNameEn
 * @method static EntityFieldSignature F_organizationNameEn() The Signature of: Field `organizationNameEn`
 * @method static ConditionField C_organizationNameEn() The Condition Field of: Field `organizationNameEn`
 * @property ?string organizationNationalId
 * @method static EntityFieldSignature F_organizationNationalId() The Signature of: Field `organizationNationalId`
 * @method static ConditionField C_organizationNationalId() The Condition Field of: Field `organizationNationalId`
 * @property string cellphoneNumber
 * @method static EntityFieldSignature F_cellphoneNumber() The Signature of: Field `cellphoneNumber`
 * @method static ConditionField C_cellphoneNumber() The Condition Field of: Field `cellphoneNumber`
 * @property string email
 * @method static EntityFieldSignature F_email() The Signature of: Field `email`
 * @method static ConditionField C_email() The Condition Field of: Field `email`
 * @property string address
 * @method static EntityFieldSignature F_address() The Signature of: Field `address`
 * @method static ConditionField C_address() The Condition Field of: Field `address`
 * @property null|array|object geolocation
 * @method static EntityFieldSignature F_geolocation() The Signature of: Field `geolocation`
 * @method static ConditionField C_geolocation() The Condition Field of: Field `geolocation`
 * @property ?string postalCode
 * @method static EntityFieldSignature F_postalCode() The Signature of: Field `postalCode`
 * @method static ConditionField C_postalCode() The Condition Field of: Field `postalCode`
 * @property ?string landlinePhoneNumber
 * @method static EntityFieldSignature F_landlinePhoneNumber() The Signature of: Field `landlinePhoneNumber`
 * @method static ConditionField C_landlinePhoneNumber() The Condition Field of: Field `landlinePhoneNumber`
 * @property ?string faxNumber
 * @method static EntityFieldSignature F_faxNumber() The Signature of: Field `faxNumber`
 * @method static ConditionField C_faxNumber() The Condition Field of: Field `faxNumber`
 * @property ?string iban
 * @method static EntityFieldSignature F_iban() The Signature of: Field `iban`
 * @method static ConditionField C_iban() The Condition Field of: Field `iban`
 * @property string website
 * @method static EntityFieldSignature F_website() The Signature of: Field `website`
 * @method static ConditionField C_website() The Condition Field of: Field `website`
 * @property string referral
 * @method static EntityFieldSignature F_referral() The Signature of: Field `referral`
 * @method static ConditionField C_referral() The Condition Field of: Field `referral`
 * @property Session[] sessions
 * @method static EntityFieldSignature F_sessions() The Signature of: Field `sessions`
 * @method static ConditionField C_sessions() The Condition Field of: Field `sessions`
 */
class User extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'personType' => new EntityFieldSignature(
                static::class, 'personType',
                new Enum(['values' => ['juridical', 'natural']]),
                null
            ),
            # Natural Person / Juridical Person Agent Information
            'firstNameFa' => new EntityFieldSignature(
                static::class, 'firstNameFa',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'firstNameEn' => new EntityFieldSignature(
                static::class, 'firstNameEn',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'lastNameFa' => new EntityFieldSignature(
                static::class, 'lastNameFa',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'lastNameEn' => new EntityFieldSignature(
                static::class, 'lastNameEn',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'nationalCode' => new EntityFieldSignature(
                static::class, 'nationalCode',
                new IranNationalCode([]),
                null
            ),
            'gender' => new EntityFieldSignature(
                static::class, 'gender',
                new Enum(['values' => ['male', 'female'], 'nullable' => true]),
                null
            ),
            'birthDate' => new EntityFieldSignature(
                static::class, 'birthDate',
                new Date(['nullable' => true]),
                null
            ),
            'nationality' => new EntityFieldSignature(
                static::class, 'nationality',
                new Enum(['values' => ['iranian', 'foreign'], 'nullable' => true]),
                null
            ),
            'education' => new EntityFieldSignature(
                static::class, 'education',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'job' => new EntityFieldSignature(
                static::class, 'job',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'profilePicture' => new EntityFieldSignature(
                static::class, 'profilePicture',
                new File(['type' => 'image', 'storeExtension' => 'jpg', 'compress' => true, 'maxSize' => '2MB', 'nullable' => true]),
                null
            ),
            'nationalCardPicture' => new EntityFieldSignature(
                static::class, 'nationalCardPicture',
                new File(['type' => 'image', 'storeExtension' => 'jpg', 'compress' => true, 'maxSize' => '2MB', 'nullable' => true]),
                null
            ),
            'birthCertificatePicture' => new EntityFieldSignature(
                static::class, 'birthCertificatePicture',
                new File(['type' => 'image', 'storeExtension' => 'jpg', 'compress' => true, 'maxSize' => '2MB', 'nullable' => true]),
                null
            ),
            # Organization Information
            'organizationName' => new EntityFieldSignature(
                static::class, 'organizationName',
                new Text(['minLength' => 1, 'maxLength' => 200, 'nullable' => true]),
                null
            ),
            'organizationNameEn' => new EntityFieldSignature(
                static::class, 'organizationNameEn',
                new Text(['minLength' => 1, 'maxLength' => 200, 'nullable' => true]),
                null
            ),
            'organizationNationalId' => new EntityFieldSignature(
                static::class, 'organizationNationalId',
                new IranNationalId(['nullable' => true]),
                null
            ),
            # Contact Information
            'cellphoneNumber' => new EntityFieldSignature(
                static::class, 'cellphoneNumber',
                new IranPhone([
                    'type' => 'cellphone'
                ]),
                null
            ),
            'email' => new EntityFieldSignature(
                static::class, 'email',
                new Email([]),
                null
            ),
            'address' => new EntityFieldSignature(
                static::class, 'address',
                new Text(['minLength' => 1, 'maxLength' => 500]),
                null
            ),
            'geolocation' => new EntityFieldSignature(
                static::class, 'geolocation',
                new StructuredMap(['nullable' => true, 'structure' => [
                    'lat' => new Decimal(['integerLength' => 2, 'fractionalLength' => 10]),
                    'long' => new Decimal(['integerLength' => 3, 'fractionalLength' => 10]),
                ]]),
                null
            ),
            'postalCode' => new EntityFieldSignature(
                static::class, 'postalCode',
                new IranPostalCode(['nullable' => true]),
                null
            ),
            'landlinePhoneNumber' => new EntityFieldSignature(
                static::class, 'landlinePhoneNumber',
                new IranPhone([
                    'type' => 'landline',
                    'nullable' => true
                ]),
                null
            ),
            'faxNumber' => new EntityFieldSignature(
                static::class, 'faxNumber',
                new IranPhone([
                    'type' => 'fax',
                    'nullable' => true
                ]),
                null
            ),
            'iban' => new EntityFieldSignature(
                static::class, 'iban',
                new Iban(['nullable' => true]),
                null
            ),
            'website' => new EntityFieldSignature(
                static::class, 'website',
                new Url([]),
                null
            ),
            # Other Data
            'referral' => new EntityFieldSignature(
                static::class, 'referral',
                new Text(['minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'sessions' => new EntityFieldSignature(
                static::class, 'sessions',
                new EntityRelation([
                    'relatedEntity' => \Entities\User\Session::class,
                    'relation' => 'IN',
                    'invName' => 'user',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
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