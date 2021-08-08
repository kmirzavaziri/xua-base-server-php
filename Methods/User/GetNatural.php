<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
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
 * @property ?string postalCode
 * @method static MethodItemSignature R_postalCode() The Signature of: Response Item `postalCode`
 * @property ?string landlinePhoneNumber
 * @method static MethodItemSignature R_landlinePhoneNumber() The Signature of: Response Item `landlinePhoneNumber`
 * @property ?string iban
 * @method static MethodItemSignature R_iban() The Signature of: Response Item `iban`
 * @property ?string nationality
 * @method static MethodItemSignature R_nationality() The Signature of: Response Item `nationality`
 * @property mixed birthDate
 * @method static MethodItemSignature R_birthDate() The Signature of: Response Item `birthDate`
 * @property ?string gender
 * @method static MethodItemSignature R_gender() The Signature of: Response Item `gender`
 * @property ?string education
 * @method static MethodItemSignature R_education() The Signature of: Response Item `education`
 * @property ?string job
 * @method static MethodItemSignature R_job() The Signature of: Response Item `job`
 * @property ?string website
 * @method static MethodItemSignature R_website() The Signature of: Response Item `website`
 */
class GetNatural extends MethodView
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            User::F_firstNameEn(),
            User::F_lastNameEn(),
            User::F_address(),
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
        ];
    }

    protected function feed(): Entity
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }
        return $user;
    }
}