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
use XUA\Tools\EntityFieldSignature;

/**
 * @property int id
 * @property string personType
 * @property string firstNameFa
 * @property string firstNameEn
 * @property string lastNameFa
 * @property string lastNameEn
 * @property mixed name
 * @property mixed nameDB
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
 * @property \Entities\SimCard simCard
 * @property ?\Entities\SimCard lastSimCard
 * @property \Entities\Farm[] workingFarms
 */
class User extends Entity
{
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
            'name' => new EntityFieldSignature(
                static::class, 'name',
                new PhpVirtualField([
                    'getter' => function (Entity $entity, array $param) {
                        $sep = $param['sep'] ?? ' ';
                        return $entity->firstNameEn . $sep . $entity->lastNameEn;
                    },
                    'setter' => function (Entity &$entity, array $param, mixed $value) {
                        $sep = $param['sep'] ?? ' ';
                        [$entity->firstNameEn, $entity->lastNameEn] = explode($sep, $value);
                    },
                ]),
                null
            ),
            'nameDB' => new EntityFieldSignature(
                static::class, 'name',
                new DatabaseVirtualField(['getter' => function (array $param) {
                    $sep = $param['sep'] ?? ' ';
                    return "CONCAT(firstNameFa, '$sep', lastNameFa)";
                }]),
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
            'simCard' => new EntityFieldSignature(
                static::class, 'simCard',
                new EntityRelation([
                    'relatedEntity' => SimCard::class,
                    'relation' => 'II',
                    'invName' => 'owner',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
                ]),
                new SimCard()
            ),
            'lastSimCard' => new EntityFieldSignature(
                static::class, 'lastSimCard',
                new EntityRelation([
                    'relatedEntity' => SimCard::class,
                    'relation' => 'NI',
                    'invName' => 'lastOwners',
                    'nullable' => true,
                    'invNullable' => false,
                    'definedOn' => 'there',
                ]),
                null
            ),
            'workingFarms' => new EntityFieldSignature(
                static::class, 'workingFarms',
                new EntityRelation([
                    'relatedEntity' => Farm::class,
                    'relation' => 'NN',
                    'invName' => 'workers',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
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