<?php

namespace Entities;


use Supers\Basics\EntitySupers\DatabaseVirtualField;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
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
use XUA\Entity;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @property string personType
 * @property string firstNameFa
 * @property string firstNameEn
 * @property string lastNameFa
 * @property string lastNameEn
 * @property string nationalCode
 * @property ?string organizationName
 * @property ?string organizationNameEn
 * @property ?string organizationNationalId
 * @property string cellphoneNumber
 * @property string email
 * @property string address
 * @property null|array|object geolocation
 * @property ?string postalCode
 * @property ?string phoneNumber
 * @property ?string faxNumber
 * @property ?string iban
 */
class User extends Entity
{
    const id = 'id';
    const personType = 'personType';
    const firstNameFa = 'firstNameFa';
    const firstNameEn = 'firstNameEn';
    const lastNameFa = 'lastNameFa';
    const lastNameEn = 'lastNameEn';
    const nationalCode = 'nationalCode';
    const organizationName = 'organizationName';
    const organizationNameEn = 'organizationNameEn';
    const organizationNationalId = 'organizationNationalId';
    const cellphoneNumber = 'cellphoneNumber';
    const email = 'email';
    const address = 'address';
    const geolocation = 'geolocation';
    const postalCode = 'postalCode';
    const phoneNumber = 'phoneNumber';
    const faxNumber = 'faxNumber';
    const iban = 'iban';

    protected static function _fields(): array
    {
        return array_merge(parent::_fields(), [
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
            # Juridical Person Information
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
//                    'type' => 'cellphone'
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
            'phoneNumber' => new EntityFieldSignature(
                static::class, 'phoneNumber',
                new IranPhone([
//                    'type' => 'landline',
                    'nullable' => true
                ]),
                null
            ),
            'faxNumber' => new EntityFieldSignature(
                static::class, 'faxNumber',
                new IranPhone([
//                    'type' => 'fax',
                    'nullable' => true
                ]),
                null
            ),
            'iban' => new EntityFieldSignature(
                static::class, 'iban',
                new Iban(['nullable' => true]),
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