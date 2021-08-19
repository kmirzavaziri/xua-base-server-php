<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int id
 * @method static MethodItemSignature R_id() The Signature of: Response Item `id`
 * @property ?string firstNameFa
 * @method static MethodItemSignature R_firstNameFa() The Signature of: Response Item `firstNameFa`
 * @property ?string firstNameEn
 * @method static MethodItemSignature R_firstNameEn() The Signature of: Response Item `firstNameEn`
 * @property ?string lastNameFa
 * @method static MethodItemSignature R_lastNameFa() The Signature of: Response Item `lastNameFa`
 * @property ?string lastNameEn
 * @method static MethodItemSignature R_lastNameEn() The Signature of: Response Item `lastNameEn`
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
 * @property ?\Services\XUA\FileInstance birthCertificatePicture
 * @method static MethodItemSignature R_birthCertificatePicture() The Signature of: Response Item `birthCertificatePicture`
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
 * @property ?string referralMethod
 * @method static MethodItemSignature R_referralMethod() The Signature of: Response Item `referralMethod`
 * @property ?string referralDetails
 * @method static MethodItemSignature R_referralDetails() The Signature of: Response Item `referralDetails`
 * @property bool verified
 * @method static MethodItemSignature R_verified() The Signature of: Response Item `verified`
 * @property \Entities\User\Session[] sessions
 * @method static MethodItemSignature R_sessions() The Signature of: Response Item `sessions`
 * @property bool admin
 * @method static MethodItemSignature R_admin() The Signature of: Response Item `admin`
 */
class GetOne extends GetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        $fields = User::fieldSignatures();
        unset($fields['sessions']);
        return VarqueMethodFieldSignature::fromList($fields);
    }
}