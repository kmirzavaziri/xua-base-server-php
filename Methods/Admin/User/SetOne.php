<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?string Q_firstNameFa
 * @method static MethodItemSignature Q_firstNameFa() The Signature of: Request Item `firstNameFa`
 * @property ?string Q_firstNameEn
 * @method static MethodItemSignature Q_firstNameEn() The Signature of: Request Item `firstNameEn`
 * @property ?string Q_lastNameFa
 * @method static MethodItemSignature Q_lastNameFa() The Signature of: Request Item `lastNameFa`
 * @property ?string Q_lastNameEn
 * @method static MethodItemSignature Q_lastNameEn() The Signature of: Request Item `lastNameEn`
 * @property ?string Q_nationalCode
 * @method static MethodItemSignature Q_nationalCode() The Signature of: Request Item `nationalCode`
 * @property ?string Q_gender
 * @method static MethodItemSignature Q_gender() The Signature of: Request Item `gender`
 * @property null|\Services\XUA\DateTimeInstance Q_birthDate
 * @method static MethodItemSignature Q_birthDate() The Signature of: Request Item `birthDate`
 * @property ?string Q_nationality
 * @method static MethodItemSignature Q_nationality() The Signature of: Request Item `nationality`
 * @property ?string Q_education
 * @method static MethodItemSignature Q_education() The Signature of: Request Item `education`
 * @property ?string Q_job
 * @method static MethodItemSignature Q_job() The Signature of: Request Item `job`
 * @property ?string Q_organizationNameFa
 * @method static MethodItemSignature Q_organizationNameFa() The Signature of: Request Item `organizationNameFa`
 * @property ?string Q_organizationNameEn
 * @method static MethodItemSignature Q_organizationNameEn() The Signature of: Request Item `organizationNameEn`
 * @property ?string Q_organizationNationalId
 * @method static MethodItemSignature Q_organizationNationalId() The Signature of: Request Item `organizationNationalId`
 * @property ?string Q_organizationRegistrationId
 * @method static MethodItemSignature Q_organizationRegistrationId() The Signature of: Request Item `organizationRegistrationId`
 * @property ?string Q_cellphoneNumber
 * @method static MethodItemSignature Q_cellphoneNumber() The Signature of: Request Item `cellphoneNumber`
 * @property ?string Q_email
 * @method static MethodItemSignature Q_email() The Signature of: Request Item `email`
 * @property ?string Q_address
 * @method static MethodItemSignature Q_address() The Signature of: Request Item `address`
 * @property null|int|float Q_geolocationLat
 * @method static MethodItemSignature Q_geolocationLat() The Signature of: Request Item `geolocationLat`
 * @property null|int|float Q_geolocationLong
 * @method static MethodItemSignature Q_geolocationLong() The Signature of: Request Item `geolocationLong`
 * @property ?string Q_postalCode
 * @method static MethodItemSignature Q_postalCode() The Signature of: Request Item `postalCode`
 * @property ?string Q_landlinePhoneNumber
 * @method static MethodItemSignature Q_landlinePhoneNumber() The Signature of: Request Item `landlinePhoneNumber`
 * @property ?string Q_faxNumber
 * @method static MethodItemSignature Q_faxNumber() The Signature of: Request Item `faxNumber`
 * @property ?string Q_website
 * @method static MethodItemSignature Q_website() The Signature of: Request Item `website`
 * @property ?string Q_personType
 * @method static MethodItemSignature Q_personType() The Signature of: Request Item `personType`
 * @property ?string Q_iban
 * @method static MethodItemSignature Q_iban() The Signature of: Request Item `iban`
 * @property ?string Q_referralMethod
 * @method static MethodItemSignature Q_referralMethod() The Signature of: Request Item `referralMethod`
 * @property ?string Q_referralDetails
 * @method static MethodItemSignature Q_referralDetails() The Signature of: Request Item `referralDetails`
 * @property bool Q_verified
 * @method static MethodItemSignature Q_verified() The Signature of: Request Item `verified`
 * @property bool Q_admin
 * @method static MethodItemSignature Q_admin() The Signature of: Request Item `admin`
 */
class SetOne extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        $fields = User::fieldSignatures();
        unset($fields['id']);
        unset($fields['profilePicture']);
        unset($fields['nationalCardPicture']);
        unset($fields['birthCertificatePicture']);
        unset($fields['sessions']);
        return VarqueMethodFieldSignature::fromList($fields, false, null, false);
    }

    protected function validations(): void
    {
        parent::validations();
        if ($this->Q_id == UserService::user()->id) {
            if (!$this->Q_admin) {
                $this->addAndThrowError('admin', ExpressionService::get('errormessage.cannot.degrade.yourself'));
            }
        }
    }
}