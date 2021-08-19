<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodView;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;


/**
 * @property ?string firstNameEn
 * @method static MethodItemSignature R_firstNameEn() The Signature of: Response Item `firstNameEn`
 * @property ?string lastNameEn
 * @method static MethodItemSignature R_lastNameEn() The Signature of: Response Item `lastNameEn`
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
 * @property ?string iban
 * @method static MethodItemSignature R_iban() The Signature of: Response Item `iban`
 * @property ?string nationality
 * @method static MethodItemSignature R_nationality() The Signature of: Response Item `nationality`
 * @property null|\Services\XUA\DateTimeInstance birthDate
 * @method static MethodItemSignature R_birthDate() The Signature of: Response Item `birthDate`
 * @property ?string gender
 * @method static MethodItemSignature R_gender() The Signature of: Response Item `gender`
 * @property ?string education
 * @method static MethodItemSignature R_education() The Signature of: Response Item `education`
 * @property ?string job
 * @method static MethodItemSignature R_job() The Signature of: Response Item `job`
 * @property ?string website
 * @method static MethodItemSignature R_website() The Signature of: Response Item `website`
 * @property ?\Services\XUA\FileInstance nationalCardPicture
 * @method static MethodItemSignature R_nationalCardPicture() The Signature of: Response Item `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance birthCertificatePicture
 * @method static MethodItemSignature R_birthCertificatePicture() The Signature of: Response Item `birthCertificatePicture`
 */
class GetNatural extends MethodView
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_firstNameEn(),
            User::F_lastNameEn(),
            User::F_address(),
            User::F_geolocationLat(),
            User::F_geolocationLong(),
            User::F_postalCode(),
            User::F_landlinePhoneNumber(),
            User::F_iban(),
            User::F_nationality(),
            User::F_birthDate(),
            User::F_gender(),
            User::F_education(),
            User::F_job(),
            User::F_website(),
            User::F_nationalCardPicture(),
            User::F_birthCertificatePicture(),
        ]);
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }
}