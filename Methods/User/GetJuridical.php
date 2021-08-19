<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodView;


/**
 * @property ?string organizationNameFa
 * @method static MethodItemSignature R_organizationNameFa() The Signature of: Response Item `organizationNameFa`
 * @property ?string organizationNameEn
 * @method static MethodItemSignature R_organizationNameEn() The Signature of: Response Item `organizationNameEn`
 * @property ?string organizationNationalId
 * @method static MethodItemSignature R_organizationNationalId() The Signature of: Response Item `organizationNationalId`
 * @property ?string organizationRegistrationId
 * @method static MethodItemSignature R_organizationRegistrationId() The Signature of: Response Item `organizationRegistrationId`
 * @property ?string faxNumber
 * @method static MethodItemSignature R_faxNumber() The Signature of: Response Item `faxNumber`
 */
class GetJuridical extends MethodView
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_organizationNameFa(),
            User::F_organizationNameEn(),
            User::F_organizationNationalId(),
            User::F_organizationRegistrationId(),
            User::F_faxNumber(),
        ]);
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }
}