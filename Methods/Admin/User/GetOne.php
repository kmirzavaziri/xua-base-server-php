<?php

namespace Methods\Admin\User;

use Entities\Farm;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?string firstNameFa
 * @method static MethodItemSignature R_firstNameFa() The Signature of: Response Item `firstNameFa`
 * @property ?string firstNameEn
 * @method static MethodItemSignature R_firstNameEn() The Signature of: Response Item `firstNameEn`
 * @property string titleFa
 * @method static MethodItemSignature R_titleFa() The Signature of: Response Item `titleFa`
 * @property ?string lastNameFa
 * @method static MethodItemSignature R_lastNameFa() The Signature of: Response Item `lastNameFa`
 * @property ?string lastNameEn
 * @method static MethodItemSignature R_lastNameEn() The Signature of: Response Item `lastNameEn`
 * @property string titleEn
 * @method static MethodItemSignature R_titleEn() The Signature of: Response Item `titleEn`
 * @property ?string nationalCode
 * @method static MethodItemSignature R_nationalCode() The Signature of: Response Item `nationalCode`
 * @property ?string gender
 * @method static MethodItemSignature R_gender() The Signature of: Response Item `gender`
 * @property null|\Services\XUA\DateTimeInstance birthDate
 * @method static MethodItemSignature R_birthDate() The Signature of: Response Item `birthDate`
 * @property ?string nationality
 * @method static MethodItemSignature R_nationality() The Signature of: Response Item `nationality`
 * @property ?string education
 * @method static MethodItemSignature R_education() The Signature of: Response Item `education`
 * @property ?string job
 * @method static MethodItemSignature R_job() The Signature of: Response Item `job`
 * @property ?\Services\XUA\FileInstance profilePicture
 * @method static MethodItemSignature R_profilePicture() The Signature of: Response Item `profilePicture`
 * @property ?\Services\XUA\FileInstance nationalCardPicture
 * @method static MethodItemSignature R_nationalCardPicture() The Signature of: Response Item `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance idBookletPicture
 * @method static MethodItemSignature R_idBookletPicture() The Signature of: Response Item `idBookletPicture`
 * @property ?string organizationNameFa
 * @method static MethodItemSignature R_organizationNameFa() The Signature of: Response Item `organizationNameFa`
 * @property ?string organizationNameEn
 * @method static MethodItemSignature R_organizationNameEn() The Signature of: Response Item `organizationNameEn`
 * @property ?string organizationNationalId
 * @method static MethodItemSignature R_organizationNationalId() The Signature of: Response Item `organizationNationalId`
 * @property ?string organizationRegistrationId
 * @method static MethodItemSignature R_organizationRegistrationId() The Signature of: Response Item `organizationRegistrationId`
 * @property ?string cellphoneNumber
 * @method static MethodItemSignature R_cellphoneNumber() The Signature of: Response Item `cellphoneNumber`
 * @property ?string email
 * @method static MethodItemSignature R_email() The Signature of: Response Item `email`
 * @property ?string address
 * @method static MethodItemSignature R_address() The Signature of: Response Item `address`
 * @property null|int|float geolocationLat
 * @method static MethodItemSignature R_geolocationLat() The Signature of: Response Item `geolocationLat`
 * @property null|int|float geolocationLong
 * @method static MethodItemSignature R_geolocationLong() The Signature of: Response Item `geolocationLong`
 * @property ?string postalCode
 * @method static MethodItemSignature R_postalCode() The Signature of: Response Item `postalCode`
 * @property ?string landlinePhoneNumber
 * @method static MethodItemSignature R_landlinePhoneNumber() The Signature of: Response Item `landlinePhoneNumber`
 * @property ?string faxNumber
 * @method static MethodItemSignature R_faxNumber() The Signature of: Response Item `faxNumber`
 * @property ?string website
 * @method static MethodItemSignature R_website() The Signature of: Response Item `website`
 * @property ?string personType
 * @method static MethodItemSignature R_personType() The Signature of: Response Item `personType`
 * @property ?string iban
 * @method static MethodItemSignature R_iban() The Signature of: Response Item `iban`
 * @property ?string bankAccountNo
 * @method static MethodItemSignature R_bankAccountNo() The Signature of: Response Item `bankAccountNo`
 * @property ?string bankTitle
 * @method static MethodItemSignature R_bankTitle() The Signature of: Response Item `bankTitle`
 * @property ?string referralMethod
 * @method static MethodItemSignature R_referralMethod() The Signature of: Response Item `referralMethod`
 * @property ?string referralDetails
 * @method static MethodItemSignature R_referralDetails() The Signature of: Response Item `referralDetails`
 * @property bool verified
 * @method static MethodItemSignature R_verified() The Signature of: Response Item `verified`
 * @property bool admin
 * @method static MethodItemSignature R_admin() The Signature of: Response Item `admin`
 * @property array farms
 * @method static MethodItemSignature R_farms() The Signature of: Response Item `farms`
 * @property array ratedFarms
 * @method static MethodItemSignature R_ratedFarms() The Signature of: Response Item `ratedFarms`
 * @property array ratedProducts
 * @method static MethodItemSignature R_ratedProducts() The Signature of: Response Item `ratedProducts`
 */
class GetOne extends GetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_firstNameFa(),
            User::F_firstNameEn(),
            User::F_titleFa(),
            User::F_lastNameFa(),
            User::F_lastNameEn(),
            User::F_titleEn(),
            User::F_nationalCode(),
            User::F_gender(),
            User::F_birthDate(),
            User::F_nationality(),
            User::F_education(),
            User::F_job(),
            User::F_profilePicture(),
            User::F_nationalCardPicture(),
            User::F_idBookletPicture(),
            User::F_organizationNameFa(),
            User::F_organizationNameEn(),
            User::F_organizationNationalId(),
            User::F_organizationRegistrationId(),
            User::F_cellphoneNumber(),
            User::F_email(),
            User::F_address(),
            User::F_geolocationLat(),
            User::F_geolocationLong(),
            User::F_postalCode(),
            User::F_landlinePhoneNumber(),
            User::F_faxNumber(),
            User::F_website(),
            User::F_personType(),
            User::F_iban(),
            User::F_bankAccountNo(),
            User::F_bankTitle(),
            User::F_referralMethod(),
            User::F_referralDetails(),
            User::F_verified(),
            User::F_admin(),
            (new EntityFieldSignatureTree(User::F_farms()))->addChildren([
                Farm::F_id(),
                Farm::F_title(),
            ]),
            (new EntityFieldSignatureTree(User::F_ratedFarms()))->addChild(
                (new EntityFieldSignatureTree(\Entities\Farm\Rate::F_farm()))->addChildren([
                    Farm::F_id(),
                    Farm::F_title(),
                ])
            ),
            (new EntityFieldSignatureTree(User::F_ratedProducts()))->addChild(
                (new EntityFieldSignatureTree(\Entities\Product\Rate::F_product()))->addChildren([
                    Product::F_id(),
                    Product::F_title(),
                ])
            ),
        ]);
    }
}